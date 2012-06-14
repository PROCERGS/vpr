<?php
class CedulaQueries {
	const SQL_FIND_BY_GRUPO_DEMANDA_AND_VOTACAO_AND_REGIAO = <<<EOD
SELECT
    *
FROM 
    `cedula` c
WHERE
    c.`fg_ativo` = 1
    AND c.`id_grupo_demanda` = :id_grupo_demanda
    AND c.`id_votacao` = :id_votacao
    AND c.`id_regiao` = :id_regiao
EOD;
}