<?php 
class Auth extends AppController {
	public static function login() {
		self::addJavascript('/js/login.js');
		
		self::setPageName("Votação de Prioridades - Orçamento 2013");
		if ($_POST) {
			try {
				$votacao = Votacao::findByActiveVotacao();
				if (count($votacao) == 0)
					throw new AppException("A votação não está aberta.", AppException::INFO);
				
				$cidadao = self::getParam('Cidadao');
				$cidadao = Cidadao::cast(Cidadao::getFromArray($cidadao));
				$phone = $cidadao->getNroTelefone();
				$email = $cidadao->getDsEmail();
				
				if (strlen($cidadao->getNroTitulo()) <= 0 || strlen($cidadao->getRg()) <= 0)
					throw new AppException("Informe o Título de Eleitor e o Número da Identidade (RG)", AppException::ERROR, array('controller' => 'Auth', 'action' => 'login'));
				
				$cidadao = Cidadao::auth($cidadao->getNroTitulo(), $cidadao->getRg());
				
				if (strlen($phone) > 0)
					$cidadao->setNroTelefone($phone);
				if (strlen($email) > 0)
					$cidadao->setDsEmail($email);
				
				if (strlen($phone) > 0 || strlen($email) > 0)
					$cidadao->update();
				
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
