<?php
class Group extends Model {

	protected static $__schema = 'pvp';

	protected $group_id;
	protected $max_selections;
	protected $expires;
	protected $created;
	protected $active;

	/**
	 * @return Group
	 */
	public static function cast($o) { return $o; }
	
	public function getOptions() {
		$options = Option::findByGroupId($this->getGroupId());
		return $options;
	}
	
	
	private function shuffleOptions() {
		$options = Option::findByGroupId($this->getGroupId());
		shuffle($options);
	
		return $options;
	}
	
	public function getShuffledOptions() {
		$options = Session::get('options');
		$group = $this->getGroupId();
		if (is_null($options)) {
			$options = array( $group => $this->shuffleOptions() );
			Session::set('options', $options);
		}
		$options = Session::get('options');
		if (!array_key_exists($group, $options)) {
			$options[$group] = $this->getShuffled($group);
			Session::set('options', $options);
		}
		return $options[$group];
	}
	
	public function getStep($step) {
		$options = $this->getShuffledOptions();
		$pageSize = Config::get('votes.pageSize');
		$pages = array_chunk($options, $pageSize);
	
		if ($step > 0 && $step <= count($pages)) {
			return array(
					'content' => $pages[$step-1],
					'pages' => count($pages),
					'pageSize' => $pageSize
			);
		}
	}
	
	public static function findStartGroup() {
		$sql = GroupQueries::SQL_FIND_START_GROUP;
		$query = PDOUtils::getConn()->prepare($sql);
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
	
	public static function findNextGroup($current_sequence) {
		$sql = GroupQueries::SQL_FIND_NEXT_GROUP;
		$query = PDOUtils::getConn()->prepare($sql);
		$query->bindValue('sequence', $current_sequence);
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
	
	/**
	 * @return Group
	 */
	public static function getCurrentGroup() {
		if (is_null(Session::get('currentGroup'))) {
			$group = Group::cast(Group::findStartGroup(0));
			if (count($group) > 0)
				Session::set('currentGroup', reset($group));
		}
		return Session::get('currentGroup');
	}
	
	public function getNextGroup($updateSession = FALSE) {
		$next_group = self::findNextGroup($this->getSequence());
		
		if (count($next_group) > 0) {
			$next_group = reset($next_group);
			if ($updateSession)
				Session::set('currentGroup', $next_group);
		} else
			$next_group = NULL;
		return $next_group;
	}
}
