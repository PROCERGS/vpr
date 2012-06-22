<?php
class MeioVotacao extends Model {

	const SMS = 3;
	
	protected static $__schema = 'seplag_vpr';

	protected $id_meio_votacao;
	protected $nm_meio_votacao;

	/**
	 * @return MeioVotacao
	 */
	public static function cast($o) { return $o; }
}
