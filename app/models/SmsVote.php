<?php
class SmsVote extends Model {
	
	const DELIMITER = '#';
	
	protected $id_sms;
	protected $from;
	protected $to;
	protected $msg;
	protected $account;
	
	protected $titulo;
	protected $rg;
	protected $options;
	protected $votacao;
	protected $grupos;
	
	private $invalid_options = array();
	private $valid_options = array();
	
	
	public function __construct($votacao, $id_sms, $from, $to, $msg, $account) {
		$this->setVotacao($votacao);
		
		$this->setIdSms($id_sms);
		$this->setFrom($from);
		$this->setTo($to);
		$this->setMsg($msg);
		$this->setAccount($account);
		
		$msg = explode('#', $msg);
		
		$this->setTitulo(array_shift($msg));
		$this->setRg(array_shift($msg));
		$this->setOptions($msg);
		
		$this->checkDocs();
	}
	
	public function checkDocs() {
		if (!Cidadao::validateRG_RS($this->getRg()))
			throw new InvalidArgumentException("RG inválido.");
		
		$cidadao = Cidadao::auth($this->getTitulo(), $this->getRg());
		
		if (!($cidadao instanceof Cidadao))
			throw new InvalidArgumentException("Título de Eleitor inválido.");
		
		return TRUE;
	}
	
	public function getVotesByGroup() {
		$grupos = $this->getGrupos();
		
		$this->invalid_options = array();
		$this->valid_options = array();
		
		$options = $this->getOptions();
		
		$groupsVotes = array();
		foreach ($grupos as $grupo) {
			foreach ($options as $option) {
				$inicial = $grupo->getNroInicial();
				$final = $grupo->getNroFinal();
				if ($option >= $inicial && $option <= $final) {
					$groupsVotes[$grupo->getIdGrupoDemanda()][$option] = 1;
					$this->valid_options[] = $option;
				}
			}
			$groupsVotes[$grupo->getIdGrupoDemanda()] = array_keys($groupsVotes[$grupo->getIdGrupoDemanda()]);
		}
		
		foreach ($options as $option) {
			if (array_search($option, $this->valid_options) === FALSE) {
				$this->invalid_options[] = $option;
			}
		}
		
		return $groupsVotes;
	}
	
	public function getValidOptions() {
		$this->getVotesByGroup();
		return $this->valid_options;
	}
	public function getInvalidOptions() {
		$this->getVotesByGroup();
		return $this->invalid_options;
	}

	public function getExceededGruposLimit() {
		$exceededGroups = array();
		
		$groupsVotes = $this->getVotesByGroup();
		$grupos = $this->getGrupos();
		
		foreach ($grupos as $grupo) {
			$id = $grupo->getIdGrupoDemanda();
			$max = $grupo->getQtdMaxEscolha();
			if (count($groupsVotes[$id]) > $max)
				$exceededGroups[] = $grupo;
		}
		
		return $exceededGroups;
	}
	
	public function getGrupos() {
		if (!is_array($this->grupos))
			$this->grupos = $this->getVotacao()->findGruposDemanda();
		return $this->grupos;
	}

	public static function sanitizeMessage($msg) {
		$count = 0;
		$regex = "/(".self::DELIMITER."){2,}/";
		$sanitized = preg_replace($regex, '$1', $msg);
		if (is_null($sanitized))
			throw new ErrorException("Error sanitizing SMS vote message.");
		else
			return $sanitized;
	}
}
