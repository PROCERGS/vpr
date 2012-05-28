<?php
class VoteRegistry extends Model {

	protected static $__schema = 'pvp';

	protected $vote_registry_id;
	protected $voter_id;
	protected $group_id;
	protected $voted;
	protected $ip;

	/**
	 * @return VoteRegistry
	 */
	public static function cast($o) { return $o; }
}
