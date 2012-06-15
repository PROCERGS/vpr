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
	
	public static function step() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$group = $votingSession->getCurrentGroup();
		
		self::registerVotes();
		
		$step = self::getParam('step');
		$page = $votingSession->getStep($step);
		$nextStep = $page['pages']==$step?NULL:$step+1;
		$cedulas = $page['content'];
		$totalSteps = $page['pages'];
		
		$html = new HTMLHelper();
		
		if (!is_null($nextStep))
			$nextURL = $html->url(array('controller' => 'Election', 'action' => 'step', 'step' => $nextStep));
		else
			$nextURL = $html->url(array('controller' => 'Election', 'action' => 'review'));
		
		self::setJavascriptVar('previousStep', $step-1);
		self::setJavascriptVar('nextStep', $nextStep);
		self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Election', 'action' => 'step', 'step' => $step - 1)));
		self::setJavascriptVar('reviewURL', $html->url(array('controller' => 'Election', 'action' => 'review'))); 
		
		self::render(compact('step', 'cedulas', 'nextURL', 'totalSteps'));
	}
	
	private static function registerVotes() {
		if (!self::isPost()) return;
		$votingSession = VotingSession::requireCurrentVotingSession();
		$group = $votingSession->getCurrentGroup();
	
		$previousStep = self::getParam('votes_step');
		if (is_null($previousStep)) return;
	
		$selected = self::getParam('selected');
		if (is_null($selected)) $selected = array();
	
		if (is_numeric($previousStep)) {
			$options = $votingSession->getStep($previousStep);
			$options = $options['content'];
				
			foreach ($options as $cedula) {
				if (array_search($cedula->getIdCedula(), $selected) !== FALSE)
					Vote::addVote($cedula->getIdCedula());
				else
					Vote::remVote($cedula->getIdCedula());
			}
		} else {
			$options = Vote::getSessionVotes();
				
			foreach ($options as $cedula) {
				if (array_search($cedula->getIdCedula(), $selected) !== FALSE)
					Vote::addVote($cedula->getIdCedula());
				else
					Vote::remVote($cedula->getIdCedula());
			}
		}
	
	}
}