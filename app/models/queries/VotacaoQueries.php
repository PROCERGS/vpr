<?php
class VotacaoQueries {
	const SQL_FIND_BY_ACTIVE_VOTACAO = <<<EOD
SELECT *
FROM
	`votacao` v
WHERE
	v.`id_situacao` = 2
	AND v.`fg_ativo` = 1
	AND v.`dth_inicio` < v.`dth_fim`
EOD;
	const SQL_FIND_BY_ID_ACTIVE_VOTACAO = <<<EOD
SELECT *
FROM
	`votacao` v
WHERE
	v.`id_situacao` = 2
	AND v.`fg_ativo` = 1
	AND v.`dth_inicio` < v.`dth_fim`
	AND v.`id_votacao` = :id_votacao
EOD;
}
//AND NOW() BETWEEN v.`dth_inicio` AND v.`dth_fim` <- não retornava valores de datas no mesmo dia