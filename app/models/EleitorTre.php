<?php
class EleitorTre extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_eleitor_tre;
	protected $nm_mun_tre;
	protected $cod_mun_tre;
	protected $int_zona;
	protected $int_secao;
	protected $ds_local_votacao;
	protected $nro_titulo;
	protected $nm_eleitor;

	/**
	 * @return EleitorTre
	 */
	public static function cast($o) { return $o; }
}
