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
    array( // row #0
        'table_name' => 'area_tematica',
    ),
    array( // row #1
        'table_name' => 'audit',
    ),
    array( // row #2
        'table_name' => 'cedula',
    ),
    array( // row #3
        'table_name' => 'cedula_pop',
    ),
    array( // row #4
        'table_name' => 'cidadao',
    ),
    array( // row #5
        'table_name' => 'cuv_cidade',
    ),
    array( // row #6
        'table_name' => 'cuv_municipio',
    ),
    array( // row #7
        'table_name' => 'cuv_pessoa',
    ),
    array( // row #8
        'table_name' => 'cuv_uf',
    ),
    array( // row #9
        'table_name' => 'data_source',
    ),
    array( // row #10
        'table_name' => 'grupo_demanda',
    ),
    array( // row #11
        'table_name' => 'legacy_inserts',
    ),
    array( // row #12
        'table_name' => 'log_erros',
    ),
    array( // row #13
        'table_name' => 'meio_votacao',
    ),
    array( // row #14
        'table_name' => 'municipio',
    ),
    array( // row #15
        'table_name' => 'pais',
    ),
    array( // row #16
        'table_name' => 'poll',
    ),
    array( // row #17
        'table_name' => 'poll_answers',
    ),
    array( // row #18
        'table_name' => 'poll_option',
    ),
    array( // row #19
        'table_name' => 'poll_question',
    ),
    array( // row #20
        'table_name' => 'poll_respondent',
    ),
    array( // row #21
        'table_name' => 'pop_cidadao',
    ),
    array( // row #22
        'table_name' => 'pop_voto_web',
    ),
    array( // row #23
        'table_name' => 'regiao',
    ),
    array( // row #24
        'table_name' => 'situacao',
    ),
    array( // row #25
        'table_name' => 'tipo_regiao',
    ),
    array( // row #26
        'table_name' => 'uf',
    ),
    array( // row #27
        'table_name' => 'urna',
    ),
    array( // row #28
        'table_name' => 'urna_old',
    ),
    array( // row #29
        'table_name' => 'votacao',
    ),
    array( // row #30
        'table_name' => 'votacao_grupo_demanda',
    ),
    array( // row #31
        'table_name' => 'votacao_meio_votacao',
    ),
    array( // row #32
        'table_name' => 'voto',
    ),
    array( // row #33
        'table_name' => 'voto_log',
    ),
    array( // row #34
        'table_name' => 'voto_log_simples',
    ),
);

$conn1->beginTransaction();
foreach ($TABLES as $row1) {
    $colluns = $conn->query("SELECT column_name FROM INFORMATION_SCHEMA.columns where table_schema = 'seplag_vpr2013' and table_name = '" . $row1['table_name'] . "'");
    $sqlInser = array();
    foreach ($colluns as $col) {
        $sqlInser[0][] = '"'.$col[0].'"';
        $sqlInser[1][] = '?';
    }
    
    $sqlInser = "insert into legacy_" . $row1['table_name'] . " (" . implode(',', $sqlInser[0]) . ") values (" . implode(',', $sqlInser[1]) . ")";
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
