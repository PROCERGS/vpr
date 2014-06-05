<?php

require_once 'stuff.php';

ini_set('max_execution_time', -1);

$config = checkArguments($argv);

$privateKeyFile = $config['privateKeyFile'];
$passphrase = $config['passphrase'];

$file = 'file://' . $privateKeyFile;
$pollPrivateKey = openssl_pkey_get_private($file, $passphrase);
if (!$pollPrivateKey) {
    e("Couldn't open private key. Is the passphrase correct?");
    e($passphrase);
    e("Error returned by OpenSSL: " . openssl_error_string());
    exit(1);
} else {
    $config['pollPrivateKey'] = $pollPrivateKey;
}

$counting = array();

try {
    $conn = getPDOConnection($config);
    $sql = getObviouslyValidVotesQuery();
    $stmt = $conn->prepare($sql);

    $insertOpenVote = $conn->prepare(getInsertOpenVoteQuery());

    e("Running query...");
    $stmt->execute();
    e("Done!");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $openOptions = null;
        $options = base64_decode($row['options']);
        $votePassphrase = base64_decode($row['passphrase']);
        if (openssl_open($options, $openOptions, $votePassphrase, $pollPrivateKey) === false) {
            throw new Exception(openssl_error_string());
        }
        if (!$openOptions) {
            throw new Exception("weird! no vote for id" . $row['id']);
        }
        $openOptions = json_decode($openOptions, true);
        if ($openOptions === false) {
            throw new Exception("weird! no valid serializaded vote for id" . $row['id']);
        }

        $conn->beginTransaction();
        foreach ($openOptions as $option) {
            $openVote = array();
            $openVote['ballot_box_id'] = $row['ballot_box_id'];
            $openVote['corede_id'] = $option['corede']['id'];
            $openVote['poll_option_id'] = $option['id'];
            $openVote['auth_type'] = $row['auth_type'];
            $openVote['voter_registration'] = !empty($row['voter_registration']);
            $openVote['signature'] = null;
            $openVote['signature'] = signVote($openVote, $config);

            @$counting[$openVote['poll_option_id']] += 1;
            $insertOpenVote->execute($openVote);
        }
        $conn->commit();
    }
} catch (Exception $e) {
    $conn->rollBack();
    e($e->getMessage());
    exit(1);
}
