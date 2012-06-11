<?php
class Regiao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_regiao;
	protected $id_uf;
	protected $id_tipo_regiao;
	protected $dt_criacao;
	protected $dt_atualizacao;
	protected $nm_regiao;
	protected $int_ordem;

	/**
	 * @return Regiao
	 */
	public static function cast($o) { return $o; }
}
