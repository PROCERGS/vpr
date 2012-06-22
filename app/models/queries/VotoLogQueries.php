<?php
class VotoLogQueries {
	const SQL_FIND_BY_ID_CIDADAO_ID_VOTACAO_COMPLETED = <<<EOD
SELECT
    *
FROM
    `grupo_demanda` gd
    INNER JOIN `votacao_grupo_demanda` vgd ON vgd.`id_grupo_demanda` = gd.`id_grupo_demanda`
    INNER JOIN `votacao` v ON v.`id_votacao` = vgd.`id_votacao`
    INNER JOIN `voto_log` vl ON vl.`id_votacao` = v.`id_votacao`
WHERE
    vl.`id_cidadao` = :id_cidadao
    AND vl.`dth_fim` IS NOT NULL
    AND v.`id_votacao` = :id_votacao
EOD;
}