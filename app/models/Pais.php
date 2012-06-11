<?php
class Pais extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_pais;
	protected $sg_pais;
	protected $nm_pais;
	protected $int_ordem;

	/**
	 * @return Pais
	 */
	public static function cast($o) { return $o; }
}
