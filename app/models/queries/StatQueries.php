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
	
	const SQL_FIND_CIDADAOS_POR_REGIAO_MEIO_VOTACAO = <<<EOD
SELECT
    s.`nm_regiao`,
	mv.`nm_meio_votacao`,
	COUNT(s.`id_meio_votacao`) AS `total`
FROM (
	SELECT
		vl.`id_cidadao`, 
        vl.`id_meio_votacao`,
        r.`id_regiao`,
        r.`nm_regiao`
	FROM 
        `voto_log` vl
        INNER JOIN `cidadao` c ON c.`id_cidadao` = vl.`id_cidadao`
        INNER JOIN `municipio` m ON m.`id_municipio` = c.`id_municipio`
        INNER JOIN `regiao` r ON r.`id_regiao` = m.`id_regiao`
	WHERE
		`dth_inicio` IS NOT NULL 
		AND `dth_fim` IS NOT NULL 
	GROUP BY `id_cidadao`, `id_meio_votacao`
) s
	INNER JOIN `meio_votacao` mv ON mv.`id_meio_votacao` = s.`id_meio_votacao`
GROUP BY
	s.`id_meio_votacao`,
    s.`id_regiao`
ORDER BY
    s.`nm_regiao`
EOD;

	const SQL_FIND_VOTOS_POR_REGIAO_MEIO_VOTACAO = <<<EOD
SELECT
    r.`nm_regiao`,
	mv.`nm_meio_votacao`,
	COUNT(v.`id_meio_votacao`) AS `total`
FROM
	`voto` v
	INNER JOIN `meio_votacao` mv ON mv.`id_meio_votacao` = v.`id_meio_votacao`
	INNER JOIN `municipio` m ON m.`id_municipio` = v.`id_municipio`
	INNER JOIN `regiao` r ON r.`id_regiao` = m.`id_regiao`
GROUP BY
	v.`id_meio_votacao`,
	r.`id_regiao`
ORDER BY
	r.`nm_regiao`
EOD;
}