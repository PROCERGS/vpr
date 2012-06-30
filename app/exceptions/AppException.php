<?php
class AppException extends Exception {
	
	const ERROR = 'error';
	const WARNING = 'warning';
	const INFO = 'info';
	
	private $type;
	private $previous;
	private $extra;
	
	public function __construct($message, $type = self::ERROR, $previous = NULL, $extra = NULL) {
		parent::__construct($message);
		$this->setType($type);
		$this->setPreviousPage($previous);
		$this->setExtra($extra);
	}
	
	public function render() {
		AppController::flash($this);
		
		$votingSession = VotingSession::getCurrentVotingSession();
		if ($votingSession instanceof VotingSession && $votingSession->getCurrentUser() instanceof Cidadao)
			$cidadao = $votingSession->getCurrentUser();
		else {
			$extra = $this->getExtra();
			if ($extra['cidadao'] instanceof Cidadao)
				$cidadao = $extra['cidadao'];
			else
				$cidadao = NULL;
		}
		
		$log = new LogErros($cidadao, $this);
		$log->insert();
		
		if (!is_null($this->getPreviousPage()))
			AppController::redirect($this->getPreviousPage());
	}
	
	public function setType($type) { $this->type = $type; }
	public function getType() { return $this->type; }
	
	public function setPreviousPage($previous) { $this->previous = $previous; }
	public function getPreviousPage() { return $this->previous; }
	
	public function setExtra($extra) { $this->extra = $extra; }
	public function getExtra() { return $this->extra; }
}
