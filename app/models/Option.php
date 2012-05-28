<?php
class Option extends Model {

	protected static $__schema = 'pvp';
	protected $option_id;
	protected $group_id;
	protected $label;
	protected $description;

	/**
	 * @return Option
	 */
	public static function cast($o) { return $o; }
}
