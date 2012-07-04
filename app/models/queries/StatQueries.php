<?php
class StatQueries {
	const SQL_FIND_BY_QTD_CIDADAOS = <<<EOD
SELECT
	COUNT(*) AS `total` 
FROM (
	SELECT `id_cidadao`
	FROM  `voto_log` 
	WHERE
		`dth_inicio` IS NOT NULL 
		AND `dth_fim` IS NOT NULL 
	GROUP BY `id_cidadao`
) m
EOD;
	
	const SQL_FIND_BY_QTD_CIDADAOS_BY_MEIO_VOTACAO = <<<EOD
SELECT
	mv.`nm_meio_votacao`,
	COUNT(s.`id_meio_votacao`) AS `total`
FROM (
	SELECT
		`id_cidadao`, `id_meio_votacao`
	FROM  `voto_log`
	WHERE
		`dth_inicio` IS NOT NULL 
		AND `dth_fim` IS NOT NULL 
	GROUP BY `id_cidadao`, `id_meio_votacao`
) s
	INNER JOIN `meio_votacao` mv ON mv.`id_meio_votacao` = s.`id_meio_votacao`
	GROUP BY
		s.`id_meio_votacao`
EOD;
	
	const SQL_FIND_BY_QTD_VOTOS = <<<EOD
SELECT
	COUNT(*) AS `total`
FROM
	`voto`
EOD;
	
	const SQL_FIND_BY_QTD_VOTOS_BY_MEIO_VOTACAO = <<<EOD
SELECT
	mv.`nm_meio_votacao`,
	COUNT(v.`id_meio_votacao`) AS `total`
FROM
	`voto` v
	INNER JOIN `meio_votacao` mv ON mv.`id_meio_votacao` = v.`id_meio_votacao`
GROUP BY v.`id_meio_votacao`
EOD;
}