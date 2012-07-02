<?php
class MeioVotacao extends Model {

	const WEB = 1;
	const MOBILE = 2;
	const SMS = 3;
	
	protected static $__schema = 'seplag_vpr';

	protected $id_meio_votacao;
	protected $nm_meio_votacao;

	/**
	 * @return MeioVotacao
	 */
	public static function cast($o) { return $o; }
}
