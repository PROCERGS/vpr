<?php
class Stat extends Model {
	public static function __callStatic($name, $arguments) {
		if (Util::startsWith($name, 'find')) {
			$column = str_replace('find', '', $name);
			$column = Util::camelToUnderline($column);
			$value = NULL;
			if (is_array($arguments) && count($arguments) > 0)
				$value = $arguments[0];
			$order = NULL;
			if (array_key_exists(1, $arguments))
				$order = $arguments[1];
				
			return self::findBy($column, $value, $order);
		}
	}
	
	private static function findBy($column, $value = NULL, $order = NULL) {
		$class = get_called_class();
		$queries = $class.'Queries';
		$sql_query = strtoupper("SQL_FIND_$column");
	
		if (class_exists($queries, TRUE) && defined("$queries::$sql_query") && !is_null($sql_c = constant("$queries::$sql_query"))) {
			$sql = $sql_c;
		} else
			throw new AppException("Consulta não encontrada.", AppException::ERROR);
		
		if (!is_null($order))
			$sql .= ' ORDER BY '.join(', ', $order);
		
		$query = PDOUtils::getConn()->prepare($sql);
		if (!is_null($value)) {
			if (is_array($value)) {
				$execute = $query->execute($value);
			} else {
				$query->bindValue($column, $value);
				$execute = $query->execute();
			}
		} else
			$execute = $query->execute();
	
		if ($execute === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
        
        public static function findPollsByVotacaoId($votacao_id)
        {
            $query = PDOUtils::getConn()->prepare(StatQueries::SQL_FIND_POLLS_BY_VOTACAO_ID);
            $query->execute(compact('votacao_id'));

            $pollsStats = $query->fetchAll(PDO::FETCH_ASSOC);
            $polls = array();
            foreach ($pollsStats as $poll) {
                $polls[$poll['poll_id']]['title'] = $poll['title'];
                $polls[$poll['poll_id']]['questions'][$poll['question_id']]['question'] = $poll['question'];
                $polls[$poll['poll_id']]['questions'][$poll['question_id']]['options'][$poll['option_id']]['option'] = $poll['option'];
                $polls[$poll['poll_id']]['questions'][$poll['question_id']]['options'][$poll['option_id']]['votes'] = $poll['votes'];
            }
            
            return $polls;
        }
}
