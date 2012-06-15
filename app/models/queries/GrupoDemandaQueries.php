<?php
class GrupoDemandaQueries {
// TODO: não está pronta.
	const SQL_FIND_NEXT_AVAILABLE_GROUP = <<<EOD
SELECT
    gd.*,
    vgd.*
FROM 
    `grupo_demanda` gd
    INNER JOIN `votacao_grupo_demanda` vgd ON vgd.`id_grupo_demanda` = gd.`id_grupo_demanda`
    INNER JOIN `votacao` v ON v.`id_votacao` = vgd.`id_votacao`
    LEFT JOIN `voto_log` vl ON vl.`id_votacao` = v.`id_votacao` AND vl.`id_grupo_demanda` = gd.`id_grupo_demanda` AND vl.`id_cidadao` = :id_cidadao AND vl.`dth_fim` IS NOT NULL
WHERE
    v.`fg_ativo` = 1
    AND vgd.`fg_ativo` = 1
    AND gd.`fg_ativo` = 1
    AND NOW() BETWEEN v.`dth_inicio` AND v.`dth_fim`
    AND vl.`id_voto_log` IS NULL
    
ORDER BY
    vgd.`sequencia` ASC
LIMIT 1
EOD;
}
