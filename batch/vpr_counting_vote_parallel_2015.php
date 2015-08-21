<?php
require_once 'stuff.php';

ini_set('max_execution_time', -1);

$config = checkArguments($argv);

if (!isset($argv[2])) {
    e("Missing chunk file.");
    exit(1);
}
$chunkFile = realpath($argv[2]);

$privateKeyFile = $config['private_key_file'];
$passphrase     = $config['passphrase'];

$file           = 'file://'.$privateKeyFile;
$pollPrivateKey = openssl_pkey_get_private($file, $passphrase);
if (!$pollPrivateKey) {
    e("Couldn't open private key. Is the passphrase correct?");
    e($passphrase);
    e("Error returned by OpenSSL: ".openssl_error_string());
    exit(1);
} else {
    $config['pollPrivateKey'] = $pollPrivateKey;
}

try {
    $conn           = getPDOConnection($config);
    $insertOpenVote = $conn->prepare(getInsertOpenVoteQuery());

    $start  = date('Y-m-d H:i:s');
    e("[$start] Counting started!");
    if (($handle = fopen($chunkFile, "r")) !== FALSE) {
        $conn->beginTransaction();
        $count = 0;
        while (($data  = fgetcsv($handle)) !== FALSE) {
            $count++;
            if ($count % 1000 === 0) {
                e(sprintf("[%s] Processing entry #%s", date('Y-m-d H:i:s'),
                        $count));
            }
            $row = array(
                'id' => $data[0],
                'ballot_box_id' => $data[1],
                'corede_id' => $data[2],
                'options' => $data[3],
                'passphrase' => $data[4],
                'auth_type' => $data[5],
                'voter_registration' => $data[6] === "t" ? "true" : "false",
                'city_id' => $data[7]
            );

            $openOptions    = null;
            $options        = base64_decode($row['options']);
            $votePassphrase = base64_decode($row['passphrase']);
            if (openssl_open($options, $openOptions, $votePassphrase,
                    $pollPrivateKey) === false) {
                throw new Exception(openssl_error_string());
            }
            if (!$openOptions) {
                throw new Exception("weird! no vote for id".$row['id']);
            }

            $openOptions = json_decode($openOptions, true);
            if ($openOptions === false) {
                throw new Exception("weird! no valid serializaded vote for id".$row['id']);
            }

            if (is_array($openOptions) && count($openOptions) == 0) {
                $openVote = createOpenVote(null, $row['corede_id'], $config,
                    $row);
                $result   = $insertOpenVote->execute($openVote);
            } else {
                foreach ($openOptions as $option) {
                    $openVote = createOpenVote($option['id'],
                        $option['corede']['id'], $config, $row);
                    $insertOpenVote->execute($openVote);
                }
            }
        }
        fclose($handle);
        $conn->commit();
    }
} catch (Exception $e) {
    $conn->rollBack();
    e($e->getMessage());
    var_dump($openVote);
    exit(1);
}

$end = date('Y-m-d H:i:s');
e("[$end] Counting ended!");
