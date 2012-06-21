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
			printr("Quantidade de votos excedida para os grupos:");
			foreach ($exceeded as $group) {
				printr("\t".$group->getNmGrupoDemanda());
			}
		}
	}
}