<?php
class Situacao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_situacao;
	protected $nm_situacao;

	/**
	 * @return Situacao
	 */
	public static function cast($o) { return $o; }
}
