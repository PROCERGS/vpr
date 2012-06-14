<?php
class VotingSession extends Model {
	
	protected $current_user;
	protected $current_group;
	protected $vote_log;
	protected $votes;
	
	/**
	 * 
	 * @param Cidadao $currentUser
	 */
	public function __construct($currentUser) {
		if ($currentUser instanceof Cidadao)
			$this->setCurrentUser($currentUser);
		else
			throw new ErrorException("Invalid Cidadao.");
	}
	
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
		if (!(self::getCurrentVotingSession() instanceof VotingSession)) {
			//$currentSession = new VotingSession();
			//$currentSession->save();
			return AppController::redirect(array('controller' => 'Auth', 'action' => 'login'));
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
			$group = GrupoDemanda::findNextAvailableGroup();
			if (count($group) > 0)
				$this->setCurrentGroup(reset($group));
		}
		return $this->current_group;
	}
	
	public function requireCurrentUser() {
		$currentUser = $this->getCurrentUser();
		return $currentUser instanceof Cidadao;
	}
	public function setCurrentUser($currentUser) {
		$currentUser->fetchEleitorTre();
		$this->current_user = $currentUser;
		$this->save();
	}
	
	public function setVoteLog($voteLog) { $this->vote_log = $voteLog; $this->save(); }
	public function setVotes($votes) { $this->votes = $votes; $this->save(); }
}
