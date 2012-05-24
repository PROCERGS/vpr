<?php 
class Auth extends AppController {
	public static function login() {
		if ($_POST) {
			
			$voter = self::getParam('Voter');
			$voter = Voter::cast(Voter::getFromArray($voter));
			$voter = Voter::auth($voter->getVoterRegistration(), $voter->getBirthDate());
			
			if ($voter instanceof Voter) {
				Session::set('currentUser', $voter);
				self::redirect(array('controller' => 'Votes', 'action' => 'start'));
			} else
				echo "Acesso negado!";
		}
		self::render(compact('html'));
	}
	
	public static function logout() {
		Session::delete('currentUser');
		Session::destroy();
	}
}
