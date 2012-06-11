<?php
class GrupoDemanda extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_grupo_demanda;
	protected $nm_grupo_demanda;
	protected $nm_grupo_abrev;

	/**
	 * @return GrupoDemanda
	 */
	public static function cast($o) { return $o; }
}
