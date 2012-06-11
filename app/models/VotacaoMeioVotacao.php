<?php
class VotacaoMeioVotacao extends Model {

	protected static $__schema = 'seplag_vpr';

	protected $id_votacao_meio_votacao;
	protected $id_votacao;
	protected $id_meio_votacao;
	protected $dth_inicio;
	protected $dth_fim;

	/**
	 * @return VotacaoMeioVotacao
	 */
	public static function cast($o) { return $o; }
}
