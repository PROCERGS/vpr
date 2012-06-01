<?php
class GroupQueries {
	const SQL_FIND_START_GROUP = <<<EOD
SELECT
	*
FROM
	`group` g
WHERE
	g.`active` = 'Y'
	AND g.`expires` > NOW()
	AND (SELECT COUNT(*) FROM `option` o WHERE o.`group_id` = g.`group_id`)
ORDER BY g.`created` LIMIT 1
EOD;
	
	const SQL_FIND_NEXT_GROUP = <<<EOD
SELECT
	*
FROM
	`group` g
WHERE
	g.`active` = 'Y'
	AND g.`sequence` > :sequence
	AND (SELECT COUNT(*) FROM `option` o WHERE o.`group_id` = g.`group_id`)
ORDER BY g.`sequence` ASC
LIMIT 1
EOD;
}