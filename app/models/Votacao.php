<?php
class Votacao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_votacao;
	protected $id_situacao;
	protected $int_exercicio;
	protected $nm_votacao;

	/**
	 * @return Votacao
	 */
	public static function cast($o) { return $o; }
	
	public function checkAllowedToVote($cidadao) {
		return !$cidadao->voted($this->getIdVotacao());
	}
	/**
	 * @return array
	 */
	public function findGruposDemanda() {
		return GrupoDemanda::findByIdVotacao($this->getIdVotacao());
	}
	
	public function findAreasTematicas($id_regiao) {
		$values = array(
				'id_votacao' => $this->getIdVotacao(),
				'id_regiao' => $id_regiao);
		$areasRaw = AreaTematica::findByVotacaoRegiao($values);
		$areas = array();
		foreach ($areasRaw as $area)
			$areas[$area->getIdAreaTematica()] = $area;
		
		return $areas;
	}
}
