<?php

require_once 'stuff.php';

ini_set('max_execution_time', -1);

$config = loadConfigFromFile($argv[1]);

try {
    $conn = getPDOConnection($config);
    $allValidVotes = $conn->prepare(getAllValidVotesQuery());
    $allValidVotes->execute();


    $out = fopen('allValidVotes.csv', 'w');
    while ($row = $allValidVotes->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, $row);
    }
    fclose($out);
} catch (Exception $e) {
    e($e->getMessage());
    exit(1);
}
