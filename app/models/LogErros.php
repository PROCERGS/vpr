<?php
class LogErros extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_log_erros;
	protected $id_cidadao;
	protected $nro_titulo;
	protected $rg;
	protected $ds_email;
	protected $nro_telefone;
	protected $ds_erro;
	protected $dump;
	protected $dth_inc;
	protected $nro_ip_inc;

	/**
	 * @return LogErros
	 */
	public static function cast($o) { return $o; }
	
	/**
	 * @param Cidadao $cidadao
	 * @param AppException $exception
	 * @param mixed $dump
	 */
	public function __construct($cidadao, $exception, $dump = array()) {
		if (empty($dump)) $dump['exception'] = $exception;
		
		if (!is_null($cidadao)) {
			$this->setIdCidadao($cidadao->getIdCidadao());
			$this->setNroTitulo($cidadao->getNroTitulo());
			$this->setRg($cidadao->getRg());
			$this->setDsEmail($cidadao->getDsEmail());
			$this->setNroTelefone($cidadao->getNroTelefone());
		}
		$this->setDsErro($exception->getMessage());
		$this->setDump(serialize($dump));
		$this->setDthInc(new DateTime());
		$this->setNroIpInc($_SERVER['REMOTE_ADDR']);
	}
}
