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
		
		$sms = new SMSAPI(Config::get('sms.user'), Config::get('sms.password'));
		
		$from   = self::getParam('from');
		$to     = self::getParam('to');
		$msg    = self::getParam('msg');
		
		$return = self::registerSMS($from, $to, $msg);
		$sms->sendMessage($return['id_sms'], $from, utf8_decode($return['message']));
	}
	
	public static function fetch_messages() {
		header("Content-Type:   text/html; charset=utf-8");
		
		$sms = new SMSAPI(Config::get('sms.user'), Config::get('sms.password'));
		$messages = $sms->fetchMessages();
		
		$id_sms = 0;
		$to = self::SMS_NUMBER;
		$account = 0;
		
		printr("Processando ".count($messages)." mensagens...");
		foreach ($messages as $message) {
			$message = explode("\t", $message);
			
			$from = $message[0];
			$msg = $message[2];
			
			$id_sms = $from.date("His");
			
			printr("Processando: [$from] $msg");
			try {
				$return = self::registerSMS($id_sms, $from, $to, $msg, $account);
				$sms->sendMessage($return['id_sms'], $from, utf8_decode($return['message']));
			} catch (Exception $e) {
				printr($e->getMessage());
			}
		}
	}
	
	public static function send_messages() {
		$sms = new SMSAPI(Config::get('sms.user'), Config::get('sms.password'));
		$id = self::getParam('id');
		if (is_numeric($id) && $id > 0)
			$return = $sms->sendMessage($id, "555199674527", "Testando! :)");
	
		printr($return);
	}
	
	private static function registerSMS($from, $to, $msg) {
		
		$return = array('id_sms' => 0, 'message' => 'Não foi possível processar o voto.');
		
		$msg = SmsVote::sanitizeMessage($msg);
		$votacao = reset(Votacao::findByActiveVotacao());
		
		$req = $_REQUEST;
		$dump = compact('id_sms', 'from', 'to', 'msg', 'req');
		$log_cidadao = new Cidadao();
		$log = new LogErros($log_cidadao, new AppException("SMS RECEBIDA"), $dump);
		$log->insert();
		
		try {
			$sms_vote = new SmsVote($votacao, $from, $to, $msg);
			$return['id_sms'] = $sms_vote->getIdSms();
		} catch (Exception $e) {
			$return['message'] = $e->getMessage()." Informe apenas titulo, RG e códigos dos projetos separados por #";
			printr($return);
			self::logError(new Cidadao(), new AppException($e->getMessage()), $e);
			return $return;
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
			$return['message'] = $e->getMessage();
			return $return;
		}
		
		return $return;
	}
}