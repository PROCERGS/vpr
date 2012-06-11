<?php
class Uf extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_uf;
	protected $id_pais;
	protected $sg_uf;
	protected $nm_uf;
	protected $int_ordem;

	/**
	 * @return Uf
	 */
	public static function cast($o) { return $o; }
}
