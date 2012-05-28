<?php
class GroupQueries {
	const SQL_FIND_START_GROUP = <<<EOD
SELECT * FROM `group` g WHERE g.`active` = 'Y' AND g.`expires` > NOW() ORDER BY g.`created` LIMIT 1
EOD;
}