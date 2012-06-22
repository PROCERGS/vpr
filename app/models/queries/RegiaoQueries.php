<?php
class RegiaoQueries {
	const SQL_FIND_BY_COD_MUN_TRE = <<<EOD
SELECT
    *
FROM
    `seplag_vpr`.`regiao` r
    INNER JOIN `seplag_vpr`.`municipio` m ON m.`id_regiao` = r.`id_regiao`
WHERE
    m.`cod_mun_tre` = :cod_mun_tre
EOD;
}