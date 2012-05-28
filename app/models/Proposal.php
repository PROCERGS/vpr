<?php
class Proposal extends Model {
	protected $id;
	protected $label;
	protected $group;
	
	/**
	 * @return Proposal
	 */
	public static function cast($o) { return $o; }
	
	private static function getShuffled($group = 0) {
		$proposals = Proposal::findByGroup($group);
		shuffle($proposals);
		
		return $proposals;
	}
	
	public static function getShuffledProposals($group = 0) {
		$proposals = Session::get('proposals');
		if (is_null($proposal)) {
			$proposals = array( $group => self::getShuffled($group) );
			Session::set('proposals', $proposals);
		}
		$proposals = Session::get('proposals');
		if (!array_key_exists($group, $proposals)) {
			$proposals[$group] = self::getShuffled($group);
			Session::set('proposals', $proposals);
		}
		return $proposals[$group];
	}
	
	public static function getStep($step) {
		$proposals = self::getShuffledProposals();
		$pageSize = Config::get('votes.pageSize');
		$pages = array_chunk($proposals, $pageSize);
		
		if ($step > 0 && $step <= count($pages)) {
			return array(
						'content' => $pages[$step-1],
						'pages' => count($pages),
						'pageSize' => $pageSize
					);
		}
	}
}
