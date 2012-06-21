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
	
	public function findGruposDemanda() {
		return GrupoDemanda::findByIdVotacao($this->getIdVotacao());
	}
}
