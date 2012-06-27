<?php
class UrnaQueries {
	const SQL_FIND_BY_TXT_LOCALIZACAO_AGRUP = <<<EOD
SELECT *
FROM
    `seplag_vpr`.`urna` u
    INNER JOIN `seplag_vpr`.`municipio` m ON m.`id_municipio` = u.`id_municipio`
WHERE length(u.txt_localizacao) > 2 GROUP BY u.`id_municipio`
EOD;
	const SQL_FIND_BY_NM_MUNICIPIO = <<<EOD
SELECT
	*
FROM
    `seplag_vpr`.`urna` u
    INNER JOIN `seplag_vpr`.`municipio` m ON m.`id_municipio` = u.`id_municipio`
WHERE
	length(u.txt_localizacao) > 2
	AND  m.`nm_municipio` = :nm_municipio
ORDER BY
	u.`txt_localizacao`
EOD;
}