<?php 
class Auth extends AppController {
	public static function login() {
		if ($_POST) {
			
			$cidadao = self::getParam('Cidadao');
			$cidadao = Cidadao::cast(Cidadao::getFromArray($cidadao));
			$cidadao = Cidadao::auth($cidadao->getNroTitulo(), $cidadao->getRg());
			
			if ($cidadao instanceof Cidadao) {
				$votingSession = new VotingSession($cidadao);
				$votingSession->save();
				printr($votingSession->getCurrentUser());
				//self::redirect(array('controller' => 'Election', 'action' => 'start'));
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
