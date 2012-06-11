<?php
class TipoRegiao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_tipo_regiao;
	protected $nm_tipo_regiao;
	protected $int_ordem;

	/**
	 * @return TipoRegiao
	 */
	public static function cast($o) { return $o; }
}
