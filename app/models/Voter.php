<?php
class Voter extends Model {

	protected static $__schema = 'pvp';
	protected $voter_id;
	protected $name;
	protected $voter_registration;
	protected $birth_date;
	protected $region;

	/**
	 * @return Voter
	 */
	public static function cast($o) { return $o; }
}
