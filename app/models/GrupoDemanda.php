<?php
class GrupoDemanda extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_grupo_demanda;
	protected $id_votacao_grupo_demanda;
	protected $id_votacao;
	protected $qtd_max_item;
	protected $qtd_max_escolha;
	protected $nm_grupo_demanda;
	protected $nm_grupo_abrev;
	protected $nro_inicial;
	protected $nro_final;

	/**
	 * @return GrupoDemanda
	 */
	public static function cast($o) { return $o; }
	
	public static function findNextAvailableGroup() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$sql = GrupoDemandaQueries::SQL_FIND_NEXT_AVAILABLE_GROUP;
		$query = PDOUtils::getConn()->prepare($sql);
		$query->bindValue('id_cidadao', $votingSession->requireCurrentUser()->getIdCidadao());
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
	
	// TODO: tornar independente de VotingSession
	public function findNextGroup() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$sql = GrupoDemandaQueries::SQL_FIND_NEXT_GROUP;
		$query = PDOUtils::getConn()->prepare($sql);
		$query->bindValue('id_cidadao', $votingSession->requireCurrentUser()->getIdCidadao());
		$query->bindValue('id_grupo_demanda', $this->getIdGrupoDemanda());
		if ($query->execute() === TRUE) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
			if (count($result) > 0)
				return $result;
			else return array();
		} else
			return array();
	}
	
	public function shuffleOptions() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$region = $votingSession->requireCurrentUser()->getRegiao();
		$options = Cedula::findByGrupoDemandaVotacaoRegiao($this->getIdGrupoDemanda(), $this->getIdVotacao(), $region->getIdRegiao());
		shuffle($options);
		
		return $options;
	}
	
	public function getOptions($id_regiao) {
		$values = array(
				'id_grupo_demanda' => $this->getIdGrupoDemanda(), 
				'id_votacao' => $this->getIdVotacao(), 
				'id_regiao' => $id_regiao);
		$options = Cedula::findByGrupoDemandaVotacaoRegiao($values);
		return $options;
	}
	
	public function getAreasTematicas($id_regiao) {
		$values = array(
				'id_grupo_demanda' => $this->getIdGrupoDemanda(),
				'id_votacao' => $this->getIdVotacao(),
				'id_regiao' => $id_regiao);
		$areasRaw = AreaTematica::findByGrupoDemandaVotacaoRegiao($values);
		$areas = array();
		foreach ($areasRaw as $area)
			$areas[$area->getIdAreaTematica()] = $area;
		
		return $areas;
	}
}
