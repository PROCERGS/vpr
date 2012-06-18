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
	
	public function __construct($id_cidadao, $id_votacao, $id_grupo_demanda, $id_meio_votacao, $nro_ip) {
		$this->setDthInicio(new DateTime());
		$this->setIdCidadao($id_cidadao);
		$this->setIdVotacao($id_votacao);
		$this->setIdGrupoDemanda($id_grupo_demanda);
		$this->setIdMeioVotacao($id_meio_votacao);
		$this->setNroIp($nro_ip);
	}
}
