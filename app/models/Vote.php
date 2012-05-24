<?php
class Vote extends Model {
	protected $id;
	protected $proposal_id;
	
	/**
	 * @return Vote
	 */
	public static function cast($o) { return $o; }
	
	public static function getSessionVotes() {
		$votes = Session::get('votes');
		if (is_null($votes)) {
			$votes = array();
			Session::set('votes', $votes);
		}
		return $votes;
	}
	
	public static function isVoted($proposal) {
		$votes = self::getSessionVotes();
		if (is_object($proposal))
			$voted = isset($votes[$proposal->getId()]) && $votes[$proposal->getId()] instanceof Vote;
		else
			$voted = isset($votes[$proposal]) && $votes[$proposal] instanceof Vote;
		
		return $voted;
	}
	public static function addVote($proposal_id) {
		if (!self::isVoted($proposal_id)) {
			$votes = self::getSessionVotes();
			$vote = new Vote();
			$vote->setProposalId($proposal_id);
			$votes[$proposal_id] = $vote;
			
			Session::set('votes', $votes);
		}
	}
	public static function remVote($proposal_id) {
		if (self::isVoted($proposal_id)) {
			$votes = self::getSessionVotes();
			unset($votes[$proposal_id]);
			Session::set('votes', $votes);
		}
	}
}
