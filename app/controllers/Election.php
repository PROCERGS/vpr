<?php
class Election extends AppController {
	
	protected static function setDefaultJavascripts() {
		parent::setDefaultJavascripts();
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		self::addJavascript('/js/election.js');
	}
	
	public static function start() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		printr($votingSession->getCurrentGroup());
		
		self::render();
	} 
}