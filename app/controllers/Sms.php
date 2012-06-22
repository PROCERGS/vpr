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
		
		$msg = SmsVote::sanitizeMessage($msg);
		$votacao = reset(Votacao::findByActiveVotacao());
		
		try {
			$sms_vote = new SmsVote($votacao, $id_sms, $from, $to, $msg, $account);
		} catch (Exception $e) {
			printr($e);
			return;
		}
		
		$exceeded = $sms_vote->getExceededGruposLimit();
		
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
		
		$sms_vote->registerVotes();
		
	}
}