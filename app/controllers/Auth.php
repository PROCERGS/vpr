<?php 
class Auth extends AppController {
	public static function login() {
		if ($_POST) {
			
			$cidadao = self::getParam('Cidadao');
			$cidadao = Cidadao::cast(Cidadao::getFromArray($cidadao));
			$cidadao = Cidadao::auth($cidadao->getNroTitulo(), $cidadao->getRg());
			
			if ($cidadao instanceof Cidadao) {
				$votingSession = new VotingSession();
				$votingSession->setCurrentUser($cidadao);
				//Session::set('currentUser', $voter);
				//self::redirect(array('controller' => 'Votes', 'action' => 'start'));
				printr($cidadao);
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
