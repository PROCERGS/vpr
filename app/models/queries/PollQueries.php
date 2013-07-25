<?php
class PollQueries {
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

}