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
			printr($cidadao);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
}