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
	
	const SQL_FIND_NEXT_AVAILABLE_GROUP = <<<EOD
SELECT
	*
FROM
	`group` g
WHERE
	g.`active` = 'Y'
	AND (SELECT COUNT(*) FROM `option` o WHERE o.`group_id` = g.`group_id`) > 0
	AND :voter_id NOT IN (SELECT `voter_id` FROM `vote_log` WHERE `group_id` = g.`group_id` AND `finish` IS NOT NULL)
ORDER BY g.`sequence` ASC
LIMIT 1
EOD;
}