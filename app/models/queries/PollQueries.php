<?php

class PollQueries
{

    const SQL_FIND_LAST = <<<EOD
	SELECT * 
	  FROM poll 
  ORDER BY id DESC 
     LIMIT 1
EOD;
    const SQL_GET_QUESTIONS = <<<EOD
	SELECT * 
	  FROM poll_question
     WHERE poll_id = :poll_id
  ORDER BY sequence
EOD;
    
    const SQL_FIND_LAST_UNVOTED_BY_VOTACAO = <<<EOD
SELECT *
FROM
    poll p
    LEFT JOIN poll_respondent pr ON p.`id` = pr.`poll_id` AND pr.`cidadao_id` = :cidadao_id
WHERE
    p.`votacao_id` = :votacao_id
    AND pr.`id` IS NULL
ORDER BY p.`id` DESC
LIMIT 1
EOD;

}