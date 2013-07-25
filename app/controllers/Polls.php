<?php
class Polls extends AppController {
	
	protected static function setDefaultJavascripts() {
		parent::setDefaultJavascripts();
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
	}
	
	public static function confirm() {
        if (!self::isPost()) return;
        
        //$votingSession = VotingSession::requireCurrentVotingSession();
        //$currentUser = $votingSession->requireCurrentUser();        
        
        $answers = $_POST['poll_answers'];
        printr($answers);
        exit;
        
        self::redirect(array('controller' => 'Polls', 'action' => 'success'));
	}
    
    public static function success()
    {
        self::render();
    }

}
