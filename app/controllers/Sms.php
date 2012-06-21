<?php
class Sms extends AppController {
	
	public static function test() {
		self::render();
	}
	
	public static function receive() {
		header("Content-Type:   text/html; charset=utf-8");
		
		$id_sms = self::getParam('id');
		$from   = self::getParam('from');
		$to     = self::getParam('to');
		$msg    = self::getParam('msg');
		$account= self::getParam('account');
		
		$msg = explode('#', $msg);
		
		printr($msg);
		
		$titulo = array_shift($msg);
		$rg = array_shift($msg);
		$options = $msg;
		
		if (!Cidadao::validateRG_RS($rg)) {
			die("RG inválido!");
		}
		try {
			$cidadao = Cidadao::auth($titulo, $rg);
			if (!($cidadao instanceof Cidadao)) die("Título de Eleitor inválido.");
			
			$votacao = Votacao::cast(reset(Votacao::findByActiveVotacao()));
			$grupos = $votacao->findGruposDemanda();
			
			$groupsVotes = array();
			$registeredVotes = array();
			foreach ($grupos as $grupo) {
				foreach ($options as $option) {
					$inicial = $grupo->getNroInicial();
					$final = $grupo->getNroFinal();
					if ($option >= $inicial && $option <= $final) {
						$groupsVotes[$grupo->getIdGrupoDemanda()][$option] = 1;
						$registeredVotes[] = $option;
					}
				}
				$groupsVotes[$grupo->getIdGrupoDemanda()] = array_keys($groupsVotes[$grupo->getIdGrupoDemanda()]);
			}
			
			$invalidOptions = array();
			foreach ($options as $option) {
				if (array_search($option, $registeredVotes) === FALSE) {
					$invalidOptions[] = $option;
				}
			}
			echo "Opções válidas:";
			printr($groupsVotes);
			echo "Opções inválidas:";
			printr($invalidOptions);
			
			foreach ($grupos as $grupo) {
				$id = $grupo->getIdGrupoDemanda();
				$max = $grupo->getQtdMaxEscolha();
				if (count($groupsVotes[$id]) > $max)
					echo "Limite de $max seleções excedido no grupo $id!";
			}
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
}