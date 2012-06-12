<?php
class CidadaoQueries {
	const SQL_FIND_BY_NRO_TITULO_OR_RG = <<<EOD
SELECT *
FROM
	`cidadao` c
WHERE
	c.`nro_titulo` = :nro_titulo
	OR c.`rg` = :rg
EOD;
}
