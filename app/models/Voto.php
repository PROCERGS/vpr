<?php
class Voto extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_voto;
	protected $id_cedula;
	protected $id_municipio;
	protected $id_meio_votacao;
	protected $dth_voto;
	protected $nro_ip_inc;

	/**
	 * @return Voto
	 */
	public static function cast($o) { return $o; }
}
