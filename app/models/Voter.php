<?php
class Voter extends Model {
	protected $id;
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
		if ($voter_registration == '1234' && $birth_date == '1989-08-21') {
			$voter = new Voter();
			$voter->setId(1);
			$voter->setName("Guilherme Donato");
			$voter->setVoterRegistration($voter_registration);
			$voter->setBirthDate($birth_date);
			$voter->setRegion("Região Metropolitana");
			return $voter;
		}
		return NULL;
	}
}