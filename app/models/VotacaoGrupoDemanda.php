<?php
class VotacaoGrupoDemanda extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_votacao_grupo_demanda;
	protected $id_grupo_demanda;
	protected $id_votacao;
	protected $qtd_max_item;
	protected $qtd_max_escolha;

	/**
	 * @return VotacaoGrupoDemanda
	 */
	public static function cast($o) { return $o; }
}
