<?php
class Votes extends AppController {
	
	protected static function setDefaultJavascripts() {
		parent::setDefaultJavascripts();
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		self::addJavascript('/js/vote.js');
	}
	
	public static function start() {
		
		$votingSession = VotingSession::requireCurrentVotingSession();
		$votingSession->resetVotes();
		$group = $votingSession->getCurrentGroup();
		if (is_null($group)) {
			$group = Group::findNextAvailableGroup();
			$votingSession->setCurrentGroup($group);
		}
		$voter = $votingSession->requireCurrentUser();
		$voteLog = $votingSession->getVoteLog();
		if ($voteLog instanceof VoteLog) {
			if (!is_null($voteLog->getFinish())) {
				$votingSession->setVoteLog(VoteLog::start($voter->getVoterId(), $group->getGroupId()));
			}
		} else
			$votingSession->setVoteLog(VoteLog::start($voter->getVoterId(), $group->getGroupId()));
		
		/*$voter = Voter::requireCurrentUser();
		
		$group = Group::getCurrentGroup();
		$voteLog = VoteLog::start($voter->getVoterId(), $group->getGroupId());
		Session::set('voteLog', $voteLog);*/
		
		self::render();
	}
	
	public static function step() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$group = $votingSession->getCurrentGroup();
		
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
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$group = $votingSession->getCurrentGroup();
		
		self::registerVotes();
		
		$html = new HTMLHelper();
		
		$step1 = $group->getStep(1);
		$proposalsS = $group->getShuffledOptions();
		$votes = Vote::getSessionVotes();
		
		$next_group = $group->getNextGroup();
		if ($next_group instanceof Group)
			$next_group = TRUE;
		else
			$next_group = FALSE;
		
		self::setJavascriptVar('previousStep', $step1['pages']);
		self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Votes', 'action' => 'step', 'step' => $step1['pages'])));
		
		$proposals = array();
		foreach ($proposalsS as $proposal) {
			$proposals[$proposal->getOptionId()] = $proposal;
		}
		
		self::render(compact('votes', 'proposals', 'group', 'next_group'));
	}
	
	public static function confirm() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$group = $votingSession->getCurrentGroup();
		
		self::registerVotes();
		
		$voteLog = VoteLog::cast(Session::get('voteLog'));
		$voteLog->setFinish(new DateTime());
		$voteLog->update();
		
		$next_group = $group->getNextGroup(TRUE);
		if ($next_group instanceof Group)
			self::redirect(array('controller' => 'Votes', 'action' => 'start'));
		else
			echo "FIM";
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
