<?php

function checkArguments($argv)
{
    if (!isset($argv[1], $argv[2], $argv[3])) {
        e("Missing config file, private key or passphrase");
        exit(1);
    }
    $config = parse_ini_file($argv[1]);
    if (!$config) {
        e("Couldn't parse the config file.");
        exit(1);
    }

    $privateKeyFile = realpath($argv[2]);
    $passphrase = $argv[3];
    if (!file_exists($privateKeyFile)) {
        e("Private key file not found!");
        exit(1);
    }

    $config['privateKeyFile'] = $privateKeyFile;
    $config['passphrase'] = $passphrase;
    return $config;
}

function getObviouslyValidVotesQuery()
{
    $sql = <<<SQL
SELECT
    *
FROM
    (SELECT
        v.*
    FROM
        vote v
        INNER JOIN tre_voter_corede vc ON vc.id = v.voter_registration
    WHERE
        vc.corede_id = v.corede_id
    ) valid_corede
GROUP BY
    valid_corede.voter_registration
HAVING
    COUNT(valid_corede.voter_registration) = 1;
SQL;
    return $sql;
}

function getPDOConnection($config)
{
    $db_driver = $config['database_driver'];
    $db_host = $config['database_host'];
    $db_name = $config['database_name'];
    $db_user = $config['database_user'];
    $db_pass = $config['database_password'];
    return new PDO("$db_driver:host=$db_host;dbname=$db_name;charset=utf8",
            $db_user, $db_pass,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8")
    );
}

function e($message, $breakLinke = true)
{
    echo $message . ($breakLinke ? PHP_EOL : '');
}

function signVote($vote, $config)
{
    $privateKey = $config['pollPrivateKey'];
    $signature = false;
    openssl_sign(json_encode($vote), $signature, $privateKey);

    return base64_encode($signature);
}

function getInsertOpenVoteQuery()
{
    return 'INSERT INTO open_vote (ballot_box_id, corede_id, poll_option_id, signature, auth_type, voter_registration) VALUE (:ballot_box_id, :corede_id, :poll_option_id, :signature, :auth_type, :voter_registration)';
}