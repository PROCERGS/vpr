<?php
class CedulaQueries {
	const SQL_FIND_BY_GRUPO_DEMANDA_VOTACAO_REGIAO = <<<EOD
SELECT
    *
FROM 
    `cedula` c
    LEFT JOIN `area_tematica` at ON at.`id_area_tematica` = c.`id_area_tematica` AND at.`fg_ativo` = 1
WHERE
    c.`fg_ativo` = 1
    AND c.`id_grupo_demanda` = :id_grupo_demanda
    AND c.`id_votacao` = :id_votacao
    AND c.`id_regiao` = :id_regiao
ORDER BY
	c.`cod_projeto` ASC,
	at.`int_ordem` ASC
EOD;
	
	const SQL_FIND_BY_COD_PROJ_IN = <<<EOD
SELECT
	*
FROM
	`cedula` c
WHERE
	c.`fg_ativo` = 1
    AND c.`id_votacao` = :id_votacao
    AND c.`id_regiao` = :id_regiao
	AND c.`cod_projeto` IN (:cod_projeto:)
EOD;
}