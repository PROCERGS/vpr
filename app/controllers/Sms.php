<?php
class Sms extends AppController {
	
	const SMS_NUMBER = 28506;
	
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
		
		self::registerSMS($id_sms, $from, $to, $msg, $account);
	}
	
	public static function fetch_messages() {
		header("Content-Type:   text/html; charset=utf-8");
		
		$sms = new SMSAPI('seplag', 'ckxx55');
		$messages = $sms->fetchMessages();
		
		$id_sms = 0;
		$to = self::SMS_NUMBER;
		$account = 0;
		
		foreach ($messages as $message) {
			$message = explode("\t", $message);
			
			$from = $message[0];
			$msg = $message[2];
			
			printr("Processando: [$from] $msg");
			try {
				self::registerSMS($id_sms, $from, $to, $msg, $account);
			} catch (Exception $e) {
				printr($e->getMessage());
			}
		}
	}
	
	public static function send_messages() {
		$sms = new SMSAPI('seplag', 'ckxx55');
		$id = self::getParam('id');
		if (is_numeric($id) && $id > 0)
			$return = $sms->sendMessage($id, "555199674527", "Testando! :)");
	
		printr($return);
	}
	
	private static function registerSMS($id_sms, $from, $to, $msg, $account) {
		
		
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
			echo "RETORNO: ".$e->getMessage()." Informe apenas titulo, rg e códigos dos projetos separados por #.";
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
				printr("Votos registrados! Obrigado por participar.");
			}
		} catch (Exception $e) {
			self::logError($sms_vote->getCidadao(), new AppException($e->getMessage()), $e);
		}
	}
}