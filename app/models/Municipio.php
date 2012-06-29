<?php
class Municipio extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_municipio;
	protected $id_uf;
	protected $id_regiao;
	protected $nm_municipio;
	protected $cod_mun_tre;
	protected $cod_mun_ibge;
	protected $cod_mun_sefa;
	protected $qtd_populacao;
	protected $qtd_eleitores;
	protected $cod_mun_pop;
	protected $fg_capital;
	protected $int_ordem;

	/**
	 * @return Municipio
	 */
	public static function cast($o) { return $o; }
}
