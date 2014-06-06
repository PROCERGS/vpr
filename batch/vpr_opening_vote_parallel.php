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
    $insertOpenBalot = $conn->prepare(getInsertOpenBallotQuery());

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

            $openBallot = array();
            $openBallot['ballot_box_id'] = $row['ballot_box_id'];
            $openBallot['corede_id'] = $row['corede_id'];
            $openBallot['options'] = $openOptions;
            $openBallot['created_at'] = $row['created_at'];
            $openBallot['signature'] = null;
            $openBallot['auth_type'] = $row['auth_type'];
            $openBallot['has_nfg'] = !empty($row['nfg_cpf']);
            $openBallot['has_voter_registration'] = !empty($row['voter_registration']);
            $openBallot['has_citizen_login'] = !empty($row['login_cidadao_id']);
            $openBallot['wb_treatment_vpr'] = $row['wb_treatment_vpr'];
            $openBallot['wb_treatment_gabinete_digital'] = $row['wb_treatment_gabinete_digital'];
            //$openBallot['surveymonkey_id'] = $row['surveymonkey_id'];
            $openBallot['surveymonkey_id'] = null;
            $openBallot['city_id'] = $row['city_id'];
            $openBallot['signature'] = signVote($openBallot, $config);

            $insertOpenBalot->execute($openBallot);
            //$conn->commit();
        }
        fclose($handle);
    }
} catch (Exception $e) {
    //$conn->rollBack();
    e($e->getMessage());
    exit(1);
}
