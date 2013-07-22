<?php
class VotacaoQueries {
	const SQL_FIND_BY_ACTIVE_VOTACAO = <<<EOD
SELECT *
FROM
	`votacao` v
WHERE
	v.`id_situacao` IN (1, 2)
	AND v.`fg_ativo` = 1
	AND NOW() BETWEEN v.`dth_inicio` AND v.`dth_fim`
EOD;
	const SQL_FIND_BY_ID_ACTIVE_VOTACAO = <<<EOD
SELECT *
FROM
	`votacao` v
WHERE
	v.`id_situacao` IN (1, 2)
	AND v.`fg_ativo` = 1
	AND NOW() BETWEEN v.`dth_inicio` AND v.`dth_fim`
	AND v.`id_votacao` = :id_votacao
EOD;
	const SQL_FIND_BY_ACTIVE_VOTACAO_READ = <<<EOD
SELECT *
FROM
	`votacao` v
WHERE
    DATE_FORMAT(dth_inicio, '%Y') = DATE_FORMAT(NOW(), '%Y') 
	AND v.`id_situacao` IN (1, 2)
	AND v.`fg_ativo` = 1
EOD;
        
        const SQL_FIND_BY_MOST_RECENT = <<<EOS
SELECT *
FROM `votacao` v
WHERE
    v.`id_situacao` IN (1, 2)
    AND v.`fg_ativo` = 1
ORDER BY
    v.`dth_inicio` DESC
EOS;
}