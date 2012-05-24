<?php
class Votes extends AppController {
	
	protected static function setDefaultJavascripts() {
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		self::addJavascript('/js/vote.js');
	}
	
	public static function start() {
		$proposals = Proposal::getShuffledProposals();
		
		self::render(compact('proposals'));
	}
	
	public static function step() {
		self::registerVotes();
		
		$step = self::getParam('step');
		$page = Proposal::getStep($step);
		$nextStep = $page['pages']==$step?NULL:$step+1;
		$proposals = $page['content'];
		
		$html = new HTMLHelper();
		
		if (!is_null($nextStep))
			$nextURL = $html->url(array('controller' => 'Votes', 'action' => 'step', 'step' => $nextStep));
		else
			$nextURL = $html->url(array('controller' => 'Votes', 'action' => 'review'));
		
		self::setJavascriptVar('previousStep', $step-1);
		self::setJavascriptVar('nextStep', $nextStep);
		self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Votes', 'action' => 'step', 'step' => $step - 1)));
		
		self::render(compact('step', 'proposals', 'nextURL'));
	}
	
	public static function review() {
		self::registerVotes();
		
		$html = new HTMLHelper();
		
		$step1 = Proposal::getStep(1);
		$proposalsS = Proposal::getShuffledProposals();
		$votes = Vote::getSessionVotes();
		
		self::setJavascriptVar('previousStep', $step1['pages']);
		self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Votes', 'action' => 'step', 'step' => $step1['pages'])));
		
		$proposals = array();
		foreach ($proposalsS as $proposal) {
			$proposals[$proposal->getId()] = $proposal;
		}
		
		self::render(compact('votes', 'proposals'));
	}
	
	private static function registerVotes() {
		if (!self::isPost()) return;
		
		$previousStep = self::getParam('votes_step');
		if (is_null($previousStep)) return;
		
		$selected = self::getParam('selected');
		if (is_null($selected)) $selected = array();
		
		if (is_numeric($previousStep)) {
			$options = Proposal::getStep($previousStep);
			$options = $options['content'];
			
			foreach ($options as $proposal) {
				if (array_search($proposal->getId(), $selected) !== FALSE)
					Vote::addVote($proposal->getId());
				else
					Vote::remVote($proposal->getId());
			}
		} else {
			$options = Vote::getSessionVotes();
			
			foreach ($options as $proposal) {
				if (array_search($proposal->getProposalId(), $selected) !== FALSE)
					Vote::addVote($proposal->getProposalId());
				else
					Vote::remVote($proposal->getProposalId());
			}
		}
		
	}
}
