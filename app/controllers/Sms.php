<?php
class Sms extends AppController {
	
	public static function test() {
		self::render();
	}
	
	private static function logError($cidadao, $exception, $dump = NULL) {
		$log = new LogErros($cidadao, $exception, $dump);
		$log->insert();
	}
	
	public static function receive() {
		header("Content-Type:   text/html; charset=utf-8");
		
		$id_sms = self::getParam('id');
		$from   = self::getParam('from');
		$to     = self::getParam('to');
		$msg    = self::getParam('msg');
		$account= self::getParam('account');
		
		$msg = SmsVote::sanitizeMessage($msg);
		$votacao = reset(Votacao::findByActiveVotacao());
		
		$req = $_REQUEST;
		$dump = compact('id_sms', 'from', 'to', 'msg', 'account', 'req');
		$log_cidadao = new Cidadao();
		$log = new LogErros($log_cidadao, new AppException("SMS RECEBIDA"), $dump);
		$log->insert();
		
		try {
			$sms_vote = new SmsVote($votacao, $id_sms, $from, $to, $msg, $account);
		} catch (Exception $e) {
			printr($e);
			self::logError(new Cidadao(), new AppException($e->getMessage()), $e);
			return;
		}
		
		$exceeded = $sms_vote->getExceededGruposLimit();
		try {
			if (count($exceeded) > 0) {
				$exceeded_groups = array();
				foreach ($exceeded as $group) {
					$exceeded_groups[] = $group->getNmGrupoDemanda();
				}
				$exceeded_groups = join(', ', $exceeded_groups);
				throw new ErrorException("Quantidade de votos excedida para os grupos: $exceeded_groups");
			}
			
			$invalid = $sms_vote->getInvalidOptions();
			if (count($invalid) > 0) {
				$invalid = join(', ', $invalid);
				throw new ErrorException("As seguintes opção são inválidas: $invalid");
			}
			
			if ($sms_vote->registerVotes()) {
				printr("Votos registrados!");
			}
		} catch (Exception $e) {
			self::logError($sms_vote->getCidadao(), new AppException($e->getMessage()), $e);
		}
	}
}