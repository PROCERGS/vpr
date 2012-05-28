<?php
class Summary extends Model {

	protected static $__schema = 'pvp';
	protected $summary_id;
	protected $option_id;
	protected $votes;

	/**
	 * @return Summary
	 */
	public static function cast($o) { return $o; }
}
