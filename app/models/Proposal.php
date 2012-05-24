<?php
class Proposal extends Model {
	protected $id;
	protected $label;
	
	/**
	 * @return Proposal
	 */
	public static function cast($o) { return $o; }
	
	private static function getShuffled() {
		$proposals = Proposal::findAll();
		shuffle($proposals);
		
		return $proposals;
	}
	
	public static function getShuffledProposals() {
		if (is_null(Session::get('proposals')))
			Session::set('proposals', self::getShuffled());
		return Session::get('proposals');
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
