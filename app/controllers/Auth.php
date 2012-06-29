<?php 
class Auth extends AppController {
	public static function login() {
		if ($_POST) {
			
			$cidadao = self::getParam('Cidadao');
			$cidadao = Cidadao::cast(Cidadao::getFromArray($cidadao));
			
			$nro_titulo = trim($cidadao->getNroTitulo());
			$nro_titulo = str_pad($nro_titulo, 12, '0', STR_PAD_LEFT);
			
			$cidadao = Cidadao::auth($nro_titulo, trim($cidadao->getRg()));
			
			if ($cidadao instanceof Cidadao) {
				$votingSession = new VotingSession($cidadao);
				$votingSession->save();
				self::redirect(array('controller' => 'Election', 'action' => 'start'));
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
