<?php

require_once 'stuff.php';

ini_set('max_execution_time', -1);

$config = checkArguments($argv);

if (!isset($argv[4])) {
    e("Missing chunk file.");
    exit(1);
}
$chunkFile = realpath($argv[4]);

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
    $insertOpenVote = $conn->prepare(getInsertOpenVoteQuery());

    if (($handle = fopen($chunkFile, "r")) !== FALSE) {
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row = array(
                'id' => $data[0],
                'ballot_box_id' => $data[1],
                'corede_id' => $data[2],
                'options' => $data[3],
                'created_at' => $data[4],
                'signature' => $data[5],
                'passphrase' => $data[6],
                'auth_type' => $data[7],
                'login_cidadao_id' => $data[8],
                'nfg_cpf' => $data[9],
                'voter_registration' => $data[10],
                'wb_treatment_vpr' => $data[11],
                'wb_treatment_gabinete_digital' => $data[12],
                'surveymonkey_id' => $data[13],
                'city_id' => $data[14]
            );
            $openOptions = null;
            $options = base64_decode($row['options']);
            $votePassphrase = base64_decode($row['passphrase']);
            if (openssl_open($options, $openOptions, $votePassphrase,
                            $pollPrivateKey) === false) {
                throw new Exception(openssl_error_string());
            }
            if (!$openOptions) {
                throw new Exception("weird! no vote for id" . $row['id']);
            }

            $openOptions = json_decode($openOptions, true);
            if ($openOptions === false) {
                throw new Exception("weird! no valid serializaded vote for id" . $row['id']);
            }

            //$conn->beginTransaction();
            foreach ($openOptions as $option) {
                $openVote = array();
                $openVote['ballot_box_id'] = $row['ballot_box_id'];
                $openVote['corede_id'] = $option['corede']['id'];
                $openVote['city_id'] = $row['city_id'];
                $openVote['poll_option_id'] = $option['id'];
                $openVote['auth_type'] = $row['auth_type'];
                $openVote['voter_registration'] = !empty($row['voter_registration']);
                $openVote['signature'] = null;
                $openVote['signature'] = signVote($openVote, $config);

                $insertOpenVote->execute($openVote);
            }
            //$conn->commit();
        }
        fclose($handle);
    }
} catch (Exception $e) {
    //$conn->rollBack();
    e($e->getMessage());
    exit(1);
}
