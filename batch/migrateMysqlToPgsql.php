<?php
require_once 'stuff.php';

ini_set('max_execution_time', - 1);
ini_set('memory_limit', '2048M');

$config = loadConfigFromFile($argv[1]);
$config1 = loadConfigFromFile($argv[2]);

$conn = getPDOConnection($config);
$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NUM);

$conn1 = getPDOConnection($config1);
$conn1->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NUM);
$TABLES = array(
    /*
    array( // row #4
        'table_name' => 'corede'
    ),
    array( // row #2
        'table_name' => 'city'
    ),
    array( // row #21
        'table_name' => 'tre_voter'
    ),
    */
    array( // row #11
        'table_name' => 'poll'
    ),
    array( // row #0
        'table_name' => 'ballot_box'
    ),
    array( // row #1
        'table_name' => 'category'
    ),
    /*
    array( // row #3
        'table_name' => 'conflicting_voter_registration'
    ),
    array( // row #5
        'table_name' => 'legacy_voter_inserts'
    ),
    array( // row #6
        'table_name' => 'open_ballot'
    ),
    array( // row #7
        'table_name' => 'open_ballot_dump'
    ),
    array( // row #8
        'table_name' => 'open_ballot_no_city'
    ),
    */
    array( // row #20
        'table_name' => 'step'
    ),
    array( // row #12
        'table_name' => 'poll_option'
    ),
    /*
    array( // row #9
        'table_name' => 'open_vote'
    ),
    */
    array( // row #10
        'table_name' => 'person'
    ),
    /*
    array( // row #13
        'table_name' => 'poll_option_pop'
    ),
    */
    array( // row #14
        'table_name' => 'result_option'
    ),
    array( // row #15
        'table_name' => 'result_preview'
    ),
    array( // row #16
        'table_name' => 'result_voters'
    ),
    array( // row #17
        'table_name' => 'stats_option_vote'
    ),
    array( // row #18
        'table_name' => 'stats_total_corede_vote'
    ),
    array( // row #19
        'table_name' => 'stats_total_option_vote'
    ),
    /*
    array( // row #22
        'table_name' => 'tre_voter_corede'
    ),
    
    array( // row #23
        'table_name' => 'vote'
    ),
    */
    array( // row #24
        'table_name' => 'wb_gabinete_digital_person'
    ),
    array( // row #25
        'table_name' => 'wb_legacy_person'
    ),
    /*
    array( // row #26
        'table_name' => 'wb_legacy_person_old'
    )
    */
);

$conn1->beginTransaction();
foreach ($TABLES as $row1) {
    $colluns = $conn->query("SELECT column_name FROM INFORMATION_SCHEMA.columns where table_schema = 'seplag_vpr' and table_name = '" . $row1['table_name'] . "'");
    $sqlInser = array();
    foreach ($colluns as $col) {
        $sqlInser[0][] = $col[0];
        $sqlInser[1][] = '?';
    }
    
    $sqlInser = "insert into " . $row1['table_name'] . " (" . implode(',', $sqlInser[0]) . ") values (" . implode(',', $sqlInser[1]) . ")";
    $stm3 = $conn1->prepare($sqlInser);
    if (! $stm3) {
        print_r($conn1->errorInfo());
    }
    $L2 = 0;
    $L3 = 100000;
    while (true) {
        $stm2 = $conn->prepare("SELECT * FROM " . $row1['table_name'] . " limit $L2 , $L3");
        if (! $stm2->execute()) {
            print_r($conn->errorInfo());
            exit(1);
        }
        while ($row2 = $stm2->fetch()) {
            if (! $stm3->execute($row2)) {
                print_r($conn1->errorInfo());
                die();
            }
            $L3 --;
        }
        $stm2->closeCursor();
        
        if (! $L3) {
            $L3 = 100000;
            $L2 += $L3;
            echo date('Y-m-d H:i:s') . " " .$L2 . "\n";
        } else {
            break;
        }
    }
    echo date('Y-m-d H:i:s') . " finish " . $row1['table_name'] . " $L3\n";
    $stm3->closeCursor();
}
$conn1->commit();
exit(0);
