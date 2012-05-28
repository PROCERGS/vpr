<?php
class Voter extends Model {

	protected static $__schema = 'pvp';
	protected $voter_id;
	protected $name;
	protected $voter_registration;
	protected $birth_date;
	protected $region;

	/**
	 * @return Voter
	 */
	public static function cast($o) { return $o; }
	
	public static function requireCurrentUser() {
		$currentUser = Session::get('currentUser');
		if (!($currentUser instanceof Voter)) {
			AppController::setDestinationAfterLogin(Router::getInstance()->getFullRequestURI());
			AppController::redirect(array('controller' => 'Auth', 'action' => 'login'));
		}
		return $currentUser;
	}
	
	public static function isAuthenticated() {
		$currentUser = Session::get('currentUser');
	
		return ($currentUser instanceof Voter);
	}
	
	public static function auth($voter_registration, $birth_date) {
		$birth_date = Util::formatDate($birth_date, 'Y-m-d');
		
		$voter = reset(Voter::findByVoterRegistration($voter_registration));
		if ($voter instanceof Voter) {
			printr($voter);
			if ($voter->getBirthDate() == $birth_date)
				return $voter;
		}
		return NULL;
	}
}
