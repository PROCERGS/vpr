<?php
class VotingSession extends Model {
	
	protected $current_user;
	protected $current_group;
	protected $vote_log;
	protected $votes;
	
	/**
	 * @return VotingSession
	 */
	public static function getCurrentVotingSession() {
		$votingSession = Session::get('currentVotingSession');
		if (!($votingSession instanceof VotingSession))
			return NULL;
		else
			return $votingSession;
	}
	
	/**
	 * @return VotingSession
	 */
	public static function requireCurrentVotingSession() {
		if (!(Session::get('currentVotingSession') instanceof VotingSession)) {
			$currentSession = new VotingSession();
			$currentSession->save();
		}
		return Session::get('currentVotingSession');
	}
	
	public function save() {
		Session::set('currentVotingSession', $this);
	}
	
	public function resetVotes() {
		$this->votes = NULL;
		$this->save();
	}
	
	public function setCurrentGroup($group) { $this->current_group = $group; $this->save(); }
	public function getCurrentGroup() {
		if (is_null($this->current_group)) {
			$group = Group::cast(Group::findNextAvailableGroup());
			if (count($group) > 0)
				$this->setCurrentGroup(reset($group));
		}
		return $this->current_group;
	}
	
	public function requireCurrentUser() {
		$voter = Voter::requireCurrentUser();
		return $voter;
	}
	public function setCurrentUser($currentUser) {
		$this->current_user = $currentUser;
		$this->save();
	}
	
	public function setVoteLog($voteLog) { $this->vote_log = $voteLog; $this->save(); }
	public function setVotes($votes) { $this->votes = $votes; $this->save(); }
}
