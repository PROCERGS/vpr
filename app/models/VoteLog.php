<?php
class VoteLog extends Model {

	protected static $__schema = 'pvp';

	protected $vote_log_id;
	protected $voter_id;
	protected $group_id;
	protected $start;
	protected $finish;

	/**
	 * @return VoteLog
	 */
	public static function cast($o) { return $o; }
	
	
	public static function start($voter_id, $group_id) {
		$voteLog = new VoteLog();
		$voteLog->setGroupId($group_id);
		$voteLog->setVoterId($voter_id);
		$voteLog->setStart(new DateTime());
		
		$logId = $voteLog->insert();
		$voteLog = reset(VoteLog::findByVoteLogId($logId));
		return $voteLog;
	}
}
