<?php
class PollQuestionQueries {

const SQL_GET_OPTIONS = <<<EOD
	SELECT * 
	  FROM poll_option
	 WHERE poll_question_id = :poll_question_id
  ORDER BY cod_pop
EOD;

}