<?php
class Vote extends Model {
	protected $id;
	protected $id_cedula;
	
	/**
	 * @return Vote
	 */
	public static function cast($o) { return $o; }
	
	public static function getSessionVotes() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$votes = $votingSession->getVotes();
		if (is_null($votes)) {
			$votingSession->setVotes(array());
		}
		return $votes;
	}
	
	public static function isVoted($option) {
		$votes = self::getSessionVotes();
		if (is_object($option))
			$voted = isset($votes[$option->getIdCedula()]) && $votes[$option->getIdCedula()] instanceof Vote;
		else
			$voted = isset($votes[$option]) && $votes[$option] instanceof Vote;
		
		return $voted;
	}
	public static function addVote($option_id) {
		if (!self::isVoted($option_id)) {
			$votes = self::getSessionVotes();
			$vote = new Vote();
			$vote->setIdCedula($option_id);
			$votes[$option_id] = $vote;
			
			self::setVotes($votes);
		}
	}
	public static function remVote($option_id) {
		if (self::isVoted($option_id)) {
			$votes = self::getSessionVotes();
			unset($votes[$option_id]);
			self::setVotes($votes);
		}
	}
	
	private static function setVotes($votes) {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$votingSession->setVotes($votes);
	}
}
