<?php
class SmsVote extends Model {
	
	const DELIMITER = '#';
	const ALLOW = 'ALLOW';
	const DENY = 'DENY';
	
	protected $id_sms;
	protected $from;
	protected $to;
	protected $msg;
	protected $account;
	
	protected $cidadao;
	
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
		
		$msg = str_replace(" ", "",$msg);
		$msg = explode('#', $msg);
		
		$this->setTitulo(array_shift($msg));
		$this->setRg(array_shift($msg));
		$this->setOptions($msg);
		
		$this->checkDocs();
		
		$smsPolicy = Config::get('sms.policy');
		$allowedRegionsId = Config::get('sms.allowedRegionsId');
		if ($smsPolicy == self::ALLOW) {
			if (array_search($this->getCidadao()->getRegiao()->getIdRegiao(), $allowedRegionsId) === FALSE)
				throw new ErrorException("Votação SMS não disponível para sua região.");
		} elseif ($smsPolicy == self::DENY)
			if (array_search($this->getCidadao()->getRegiao()->getIdRegiao(), $allowedRegionsId) !== FALSE)
				throw new ErrorException("Votação SMS não disponível para sua região.");
	}
	
	public function checkDocs() {
		if (!Cidadao::validateRG_RS($this->getRg()))
			throw new InvalidArgumentException("N�mero de Identidde (RG) inválido.");
		
		$cidadao = Cidadao::auth($this->getTitulo(), $this->getRg());
		
		if (!($cidadao instanceof Cidadao))
			throw new InvalidArgumentException("Título de Eleitor não encontrado.");
		
		if (!$this->checkAllowedToVote($cidadao))
			throw new ErrorException("Título já votou.");
		
		$this->setCidadao($cidadao);
		
		return TRUE;
	}
	
	public function checkAllowedToVote($cidadao) {
		return $this->getVotacao()->checkAllowedToVote($cidadao);
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
		$sanitized = preg_replace("/(^#|[^#0-9])/", '', $sanitized);
		if (is_null($sanitized))
			throw new ErrorException("Error sanitizing SMS vote message.");
		else
			return $sanitized;
	}
	
	public function registerVotes() {
		PDOUtils::getConn()->beginTransaction();
		try {
			$cidadao = $this->getCidadao();
			$regiao = $cidadao->getRegiao();
			$votacao = $this->getVotacao();
			$id_municipio = $regiao->getIdMunicipio();
			$options = Cedula::findByCodProjeto($this->getValidOptions(), $votacao->getIdVotacao(), $regiao->getIdRegiao());
			
			$groups_voted = array();
			
			foreach ($options as $option) {
				$id_group = $option->getIdGrupoDemanda();
				if (!array_key_exists($id_group, $groups_voted))
					$groups_voted[$id_group] = 0;
				
				$voto = new Voto($option->getIdCedula(), $id_municipio, MeioVotacao::SMS, $_SERVER['REMOTE_ADDR']);
				$groups_voted[$id_group]++;
				$voto->setIdVoto($voto->insert());
			}
			
			foreach ($groups_voted as $group => $count) {
				$voto_log = new VotoLog($cidadao->getIdCidadao(), $votacao->getIdVotacao(), $group, MeioVotacao::SMS, $_SERVER['REMOTE_ADDR']);
				$voto_log->setDthFim(new DateTime());
				$voto_log->setQtdSelecoes($count);
				$voto_log->setIdVotoLog($voto_log->insert());
			}
			
			PDOUtils::getConn()->commit();
		} catch (Exception $e) {
			PDOUtils::getConn()->rollBack();
			throw $e;
		}
		
		return TRUE;
	}
}
