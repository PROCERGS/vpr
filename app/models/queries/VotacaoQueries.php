<?php
class VotacaoQueries {
	const SQL_FIND_BY_ACTIVE_VOTACAO = <<<EOD
SELECT *
FROM
	`votacao` v
WHERE
	v.`id_situacao` = 2
	AND v.`fg_ativo` = 1
	AND NOW() BETWEEN v.`dth_inicio` AND v.`dth_fim`
EOD;
}