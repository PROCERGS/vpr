<?php
class VotoLog extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_voto_log;
	protected $id_cidadao;
	protected $id_votacao;
	protected $id_grupo_demanda;
	protected $id_meio_votacao;
	protected $dth_inicio;
	protected $dth_fim;
	protected $qtd_selecoes;
	protected $nro_ip;

	/**
	 * @return VotoLog
	 */
	public static function cast($o) { return $o; }
}
