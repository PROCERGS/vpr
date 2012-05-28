<?php
class Group extends Model {

	protected static $__schema = pvp;

	protected $__schema = 'pvp';
	protected $group_id;
	protected $max_selections;
	protected $expires;
	protected $created;
	protected $active;

	/**
	 * @return Group
	 */
	public static function cast($o) { return $o; }
}
