<?php
class SMSAPI {
	
	private $user, $password;
	private $url = 'https://cgi2sms.com.br/3.0/';
	
	public function __construct($user, $password) {
		$this->setUser($user);
		$this->setPassword($password);
	}
	
	public function apiCall($method, $params = array()) {
		if (!empty($params))
			$params = '?'.http_build_query($params);
		else
			$params = '';
		
		$url = $this->getURL();
		$url .= "$method.php$params";
		
		if (Util::getEnvironmentName() == 'local') {
			$context = array('http' => array(
				'proxy' => 'tcp://proxy.procergs.reders:3128',
				'request_fulluri' => true,
				'header' => "Proxy-Authorization: Basic ".Config::get('proxy.auth')
			));
			$return = file_get_contents($url, NULL, stream_context_create($context));
		} else
			$return = self::cURLGet($url);
		
		$return = explode("\n", $return);
		
		$this->checkResult(reset($return));
		
		return $return;
	}
	
	private function getDefaultParams() {
		return array(
			'user' => $this->getUser(),
			'pass' => $this->getPassword(),
			'linesep' => 0
		);
	}
	
	private function checkResult($code) {
		switch ($code) {
			case '00': 	return TRUE;
						break;
			case '08': throw new ErrorException("Erro de acesso a banco de dados.");
			case '10': throw new ErrorException("Usuário/senha inválidos.");
			case '11': throw new ErrorException("Parâmetros inválidos.");
			case '12': throw new ErrorException("Número de telefone inválido ou não coberto pelo sistema");
			case '13': throw new ErrorException("Operadora desativada para envio de mensagens");
			case '14': throw new ErrorException("Usuário não pode enviar mensagens para esta operadora");
			case '15': throw new ErrorException("Créditos insuficientes");
			case '16': throw new ErrorException("Tempo mínimo entre duas requisições em andamento");
			case '17': throw new ErrorException("Permissão negada para a utilização do CGI/Produtos");
			case '18': throw new ErrorException("Operadora Offline");
			case '19': throw new ErrorException("Envio negado a partir do IP de origem");
			default: throw new ErrorException("Retorno inválido.");
		}
	}
	
	public function getUser() { return $this->user; }
	private function setUser($user) { $this->user = $user; }
	
	private function getPassword() { return $this->password; }
	private function setPassword($password) { $this->password = $password; }
	
	public function getURL() { return $this->url; }
	
	public function fetchMessages() {
		$params = $this->getDefaultParams();
		$messages = $this->apiCall('user_message_receive', $params);
		array_shift($messages);
		$count = array_shift($messages);
		
		array_pop($messages);
		
		if ($count != count($messages))
			throw new ErrorException("Messages count missmatch. Expected $count messages but received ".count($messages));
		
		return $messages;
	}
	
	public function sendMessage($id, $destination, $message) {
		$params = $this->getDefaultParams();
		$params['testmode'] = 0;
		$params['messages'] = "\t$destination\t\t$message\t$id";
		
		$return = $this->apiCall('user_message_send', $params);
		
		return $return;
	}
	
	private static function cURLGet($url) {
		
		$options = array(
				CURLOPT_RETURNTRANSFER => true,     // return web page
				CURLOPT_HEADER         => false,    // don't return headers
				CURLOPT_FOLLOWLOCATION => true,     // follow redirects
				CURLOPT_ENCODING       => "",       // handle all encodings
				CURLOPT_USERAGENT      => "spider", // who am i
				CURLOPT_AUTOREFERER    => true,     // set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 30,      // timeout on connect
				CURLOPT_TIMEOUT        => 30,      // timeout on response
				CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);
	
		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );
	
		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;
		printr($header);
		return $content;
	}
}