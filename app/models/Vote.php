<?php
class Vote extends Model {
	protected $id;
	protected $option_id;
	
	/**
	 * @return Vote
	 */
	public static function cast($o) { return $o; }
	
	public static function resetVotes() {
		Session::delete('votes');
	}
	
	public static function getSessionVotes() {
		$votes = Session::get('votes');
		if (is_null($votes)) {
			$votes = array();
			Session::set('votes', $votes);
		}
		return $votes;
	}
	
	public static function isVoted($option) {
		$votes = self::getSessionVotes();
		if (is_object($option))
			$voted = isset($votes[$option->getOptionId()]) && $votes[$option->getOptionId()] instanceof Vote;
		else
			$voted = isset($votes[$option]) && $votes[$option] instanceof Vote;
		
		return $voted;
	}
	public static function addVote($option_id) {
		if (!self::isVoted($option_id)) {
			$votes = self::getSessionVotes();
			$vote = new Vote();
			$vote->setOptionId($option_id);
			$votes[$option_id] = $vote;
			
			Session::set('votes', $votes);
		}
	}
	public static function remVote($option_id) {
		if (self::isVoted($option_id)) {
			$votes = self::getSessionVotes();
			unset($votes[$option_id]);
			Session::set('votes', $votes);
		}
	}
}
