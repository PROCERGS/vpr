<?php
class Votes extends AppController {
	
	protected static function setDefaultJavascripts() {
		parent::setDefaultJavascripts();
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		self::addJavascript('/js/vote.js');
	}
	
	public static function start() {
		Voter::requireCurrentUser();
		
		$group = Group::getCurrentGroup();
		
		self::render();
	}
	
	public static function step() {
		$currentUser = Voter::requireCurrentUser();
		$group = Group::getCurrentGroup();
		
		self::registerVotes();
		
		$step = self::getParam('step');
		$page = $group->getStep($step);
		$nextStep = $page['pages']==$step?NULL:$step+1;
		$proposals = $page['content'];
		$totalSteps = $page['pages'];
		
		$html = new HTMLHelper();
		
		if (!is_null($nextStep))
			$nextURL = $html->url(array('controller' => 'Votes', 'action' => 'step', 'step' => $nextStep));
		else
			$nextURL = $html->url(array('controller' => 'Votes', 'action' => 'review'));
		
		self::setJavascriptVar('previousStep', $step-1);
		self::setJavascriptVar('nextStep', $nextStep);
		self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Votes', 'action' => 'step', 'step' => $step - 1)));
		self::setJavascriptVar('reviewURL', $html->url(array('controller' => 'Votes', 'action' => 'review'))); 
		
		self::render(compact('step', 'proposals', 'nextURL', 'totalSteps'));
	}
	
	public static function review() {
		$currentUser = Voter::requireCurrentUser();
		$group = Group::getCurrentGroup();
		
		self::registerVotes();
		
		$html = new HTMLHelper();
		
		$step1 = $group->getStep(1);
		$proposalsS = $group->getShuffledOptions();
		$votes = Vote::getSessionVotes();
		
		self::setJavascriptVar('previousStep', $step1['pages']);
		self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Votes', 'action' => 'step', 'step' => $step1['pages'])));
		
		$proposals = array();
		foreach ($proposalsS as $proposal) {
			$proposals[$proposal->getOptionId()] = $proposal;
		}
		
		self::render(compact('votes', 'proposals', 'group'));
	}
	
	private static function registerVotes() {
		if (!self::isPost()) return;
		
		$group = Group::getCurrentGroup();
		
		$previousStep = self::getParam('votes_step');
		if (is_null($previousStep)) return;
		
		$selected = self::getParam('selected');
		if (is_null($selected)) $selected = array();
		
		if (is_numeric($previousStep)) {
			$options = $group->getStep($previousStep);
			$options = $options['content'];
			
			foreach ($options as $proposal) {
				if (array_search($proposal->getOptionId(), $selected) !== FALSE)
					Vote::addVote($proposal->getOptionId());
				else
					Vote::remVote($proposal->getOptionId());
			}
		} else {
			$options = Vote::getSessionVotes();
			
			foreach ($options as $proposal) {
				if (array_search($proposal->getOptionId(), $selected) !== FALSE)
					Vote::addVote($proposal->getOptionId());
				else
					Vote::remVote($proposal->getOptionId());
			}
		}
		
	}
}
