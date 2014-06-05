<?php
if (! isset($argv[1], $argv[2], $argv[3])) {
    echo "Missing config file or pem stuff";
    exit(1);
}
$config = parse_ini_file($argv[1]);
if (! $config) {
    echo "Can't parse config file";
    exit(1);
}
if (! file_exists($argv[2])) {
    echo "Don't exist pem file";
    exit(1);
}

$file = 'file://' . __DIR__ . DIRECTORY_SEPARATOR . $argv[2];
$privatePollKey = openssl_pkey_get_private($file, $config['password']);
if (! $privatePollKey) {
    echo "Mismatch pem file and pem password";
    exit(1);
}
try {
    $conn = new PDO($config['database_driver'] . ':host=' . $config['database_host'] . ';dbname=' . $config['database_name'] . ';charset=utf8', $config['database_user'], $config['database_password'], array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"
    ));
    $sql = "select * from vote limit 10";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $openVote = null;
        $options = base64_decode($row['options']);
        $passphrase = base64_decode($row['passphrase']);
        if (openssl_open($options, $openVote, $passphrase, $privatePollKey) === false) {
            throw new Exception(openssl_error_string());
        }
        if (! $openVote) {
            throw new Exception('weird! no vote for id' . $row['id']);
        }
        $openVote = json_decode($openVote, true);
        if ($openVote === false) {
            throw new Exception('weird! no valid serializaded vote for id' . $row['id']);
        }
        print_r($openVote);
        die();
    }
    $stmt = null;
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
