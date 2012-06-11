<?php
class Cedula extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_cedula;
	protected $id_votacao;
	protected $id_regiao;
	protected $id_grupo_demanda;
	protected $id_area_tematica;
	protected $nm_demanda;
	protected $ds_demanda;
	protected $vlr_demanda;
	protected $cod_projeto_sms;
	protected $nm_projeto_cedula;
	protected $int_ordem;

	/**
	 * @return Cedula
	 */
	public static function cast($o) { return $o; }
}
