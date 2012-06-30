<?php 
class Auth extends AppController {
	public static function login() {
		if ($_POST) {
			try {
				$cidadao = self::getParam('Cidadao');
				$cidadao = Cidadao::cast(Cidadao::getFromArray($cidadao));
				
				$nro_titulo = trim($cidadao->getNroTitulo());
				$nro_titulo = str_pad($nro_titulo, 12, '0', STR_PAD_LEFT);
				
				if (strlen($nro_titulo) <= 0 || strlen($cidadao->getRg()) <= 0)
					throw new AppException("Informe o Título de Eleitor e o Número da Identidade (RG)", AppException::ERROR, array('controller' => 'Auth', 'action' => 'login'));
				
				$cidadao = Cidadao::auth($nro_titulo, trim($cidadao->getRg()));
			} catch (AppException $e) {
				$extra = array(
							'titulo' => $cidadao->getNroTitulo(),
							'rg' => $cidadao->getRg(),
							'celular' => $cidadao->getNroTelefone(),
							'email' => $cidadao->getDsEmail(),
							'cidadao' => $cidadao
						);
				$e->setExtra($extra);
				throw $e;
			}
			
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
