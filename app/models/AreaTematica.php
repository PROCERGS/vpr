<?php
class AreaTematica extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_area_tematica;
	protected $nm_area_tematica;
	protected $nm_area_abrev;
	protected $int_ordem;

	/**
	 * @return AreaTematica
	 */
	public static function cast($o) { return $o; }
}
