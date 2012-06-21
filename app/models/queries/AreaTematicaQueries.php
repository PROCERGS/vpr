<?php
class AreaTematicaQueries {
	const SQL_FIND_BY_GRUPO_DEMANDA_VOTACAO_REGIAO = <<<EOD
SELECT DISTINCT
	at.*
FROM
	`area_tematica` at
	INNER JOIN `cedula` c ON at.`id_area_tematica` = c.`id_area_tematica`
WHERE
	c.`id_grupo_demanda` = :id_grupo_demanda
    AND c.`id_votacao` = :id_votacao
    AND c.`id_regiao` = :id_regiao
    AND at.`fg_ativo` = 1
	AND c.`fg_ativo` = 1
EOD;
}