<?php
class Election extends AppController {
	
	protected static function setDefaultJavascripts() {
		parent::setDefaultJavascripts();
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		self::addJavascript('/js/election.js');
	}
	
	public static function start() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$group = $votingSession->getCurrentGroup();
		
		if (!($group instanceof GrupoDemanda)) {
			throw new ErrorException("Você já votou em todos grupos disponíveis.");
		}
		
		$votoLog = $votingSession->getVotoLog();
		if (!($votoLog instanceof VotoLog)) {
			$id_meio_votacao = Config::get('isMobile')===TRUE?2:1;
			$votoLog = new VotoLog($currentUser->getIdCidadao(), $group->getIdVotacao(), $group->getIdGrupoDemanda(), $id_meio_votacao, $_SERVER['REMOTE_ADDR']);
			$votoLog->setIdVotoLog($votoLog->insert());
			$votingSession->setVotoLog($votoLog);
		}
		
		self::redirect(array('controller' => 'Election', 'action' => 'step', 'step' => 1));
	}
	
	public static function step() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$regiao = $currentUser->getRegiao();
		$group = $votingSession->getCurrentGroup();
		$areasTematicas = $group->getAreasTematicas($regiao->getIdRegiao());
		
		$maxSelections = $group->getQtdMaxEscolha();
		
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
		
		$areas = array();
		foreach ($cedulas as $option)
			$areas[$option->getIdAreaTematica()][] = $option;
		
		self::render(compact('step', 'areas', 'nextURL', 'totalSteps', 'maxSelections', 'group', 'areasTematicas'));
	}
	
	public static function review() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$group = $votingSession->getCurrentGroup();
		$options = $votingSession->getOptions($group->getIdGrupoDemanda());
		
		self::registerVotes();
		$votes = Vote::getSessionVotes();
		
		$next_group = reset($group->findNextGroup());
		if ($next_group instanceof GrupoDemanda)
			$next_group = TRUE;
		else
			$next_group = FALSE;
		
		$selection = array();
		foreach ($options as $option) {
			$selection[$option->getIdCedula()] = $option;
		}
		
		self::render(compact('options', 'votes', 'group', 'selection', 'next_group'));
	}
	
	public static function confirm() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$currentUser = $votingSession->requireCurrentUser();
		$group = $votingSession->getCurrentGroup();
	
		self::registerVotes();
		$votes = Vote::getSessionVotes();
		$votoLog = VotoLog::cast($votingSession->getVotoLog());
		
		if (count($votes) > $group->getQtdMaxEscolha()) {
			throw new UnexpectedValueException("Excedido o número de $group->getQtdMaxEscolha() opções.");
		}
		
		foreach ($votes as $vote) {
			$voto = new Voto($vote->getIdCedula(), $currentUser->getRegiao()->getIdMunicipio(), $votoLog->getIdMeioVotacao(), $_SERVER['REMOTE_ADDR']);
			$voto->setIdVoto($voto->insert());
			printr($voto);
		}
		
		$votoLog->setDthFim(new DateTime());
		$votoLog->setQtdSelecoes(count($votes));
		$votoLog->update();
		$next_group = $group->findNextGroup(TRUE);
		$votingSession->finishGroup();
		
		if ($next_group instanceof GrupoDemanda)
			echo "Redirect p/ Election::start()"; //self::redirect(array('controller' => 'Election', 'action' => 'start'));
		else
			echo "FIM";
		
		printr($votoLog);
		
		//$votoLog = VotoLog::cast($votingSession->getVotoLog());
		
/*		$voteLog = VoteLog::cast(Session::get('voteLog'));
		$voteLog->setFinish(new DateTime());
		$voteLog->update();
	
		$next_group = $group->getNextGroup(TRUE);
		if ($next_group instanceof Group)
			self::redirect(array('controller' => 'Votes', 'action' => 'start'));
		else
			echo "FIM";*/
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