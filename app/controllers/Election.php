<?php

class Election extends AppController
{

    public static function before()
    {
        parent::before();
        $action = self::getParam('action');
        //if ($action != 'success') {
            $currentUser = VotingSession::requireCurrentVotingSession()->requireCurrentUser();
            self::setPageSubName(Util::nameCamelCase($currentUser->getEleitorTre()->getNmEleitor()));
        //}
        $votacao = Votacao::findMostRecent();
        self::setPageName("Votação de Prioridades - Orçamento " . $votacao->getIntExercicio());
    }

    protected static function setDefaultJavascripts()
    {
        parent::setDefaultJavascripts();
        self::addJavascript('/js/election.js');
    }

    public static function start()
    {
        try {
            $votingSession = VotingSession::requireCurrentVotingSession();
            $currentUser = $votingSession->requireCurrentUser();
            $votacao = Votacao::findMostRecent();
            $hasPoll = $currentUser->hasPollAvailable($votacao->getIdVotacao());
            
            try {
                $group = $votingSession->getCurrentGroup();
            } catch (AppException $e) {
                if ($hasPoll) {
                    self::redirect(array('controller' => 'Election', 'action' => 'success'));
                } else {
                    throw $e;
                }
            }

            if (!($group instanceof GrupoDemanda)) {
                if ($currentUser->hasPollAvailable($votingSession->getVotacaoId())) {
                    self::redirect(array('controller' => 'Election', 'action' => 'success'));
                } else {
                    throw new ErrorException("Você já votou em todos grupos disponíveis.");
                }
            }
        } catch (Exception $e) {
            Session::delete('currentUser');
            Session::destroy();
            if ($e instanceof AppException)
                $type = $e->getType();
            else
                $type = AppException::INFO;
            throw new AppException($e->getMessage(), $type, array('controller' => 'Auth', 'action' => 'login'), array('cidadao' => $currentUser));
        }

        $votoLog = $votingSession->getVotoLog();
        if (!($votoLog instanceof VotoLog)) {
            $id_meio_votacao = Config::get('isMobile') === TRUE ? MeioVotacao::MOBILE : MeioVotacao::WEB;
            $votoLog = new VotoLog($currentUser->getIdCidadao(), $group->getIdVotacao(), $group->getIdGrupoDemanda(), $id_meio_votacao, $_SERVER['REMOTE_ADDR']);
            $votoLog->setIdVotoLog($votoLog->insert());
            $votingSession->setVotoLog($votoLog);
        }

        self::redirect(array('controller' => 'Election', 'action' => 'step', 'step' => 1));
    }

    public static function step()
    {
        $votingSession = VotingSession::requireCurrentVotingSession();
        $currentUser = $votingSession->requireCurrentUser();
        $regiao = $currentUser->getRegiao();
        $group = $votingSession->getCurrentGroup();
        $areasTematicas = $group->getAreasTematicas($regiao->getIdRegiao());

        self::registerVotes();

        $step = self::getParam('step');
        $page = $votingSession->getStep($step);
        $nextStep = $page['pages'] == $step ? NULL : $step + 1;
        $cedulas = $page['content'];
        $totalSteps = $page['pages'];

        $qtdMaxEscolha = $group->getQtdMaxEscolha();

        $html = new HTMLHelper();

        if (!is_null($nextStep))
            $nextURL = $html->url(array('controller' => 'Election', 'action' => 'step', 'step' => $nextStep));
        else {
            if (Config::get('isMobile'))
                $nextURL = $html->url(array('controller' => 'Election', 'action' => 'review'));
            else
                $nextURL = $html->url(array('controller' => 'Election', 'action' => 'confirm'));
        }

        self::setJavascriptVar('previousStep', $step - 1);
        self::setJavascriptVar('nextStep', $nextStep);
        self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Election', 'action' => 'step', 'step' => $step - 1)));
        self::setJavascriptVar('reviewURL', $html->url(array('controller' => 'Election', 'action' => 'review')));
        self::setJavascriptVar('qtdMaxEscolha', $qtdMaxEscolha);

        $areas = $group->getOptionsGroupByAreaTematica($regiao->getIdRegiao(), $cedulas);

        $groups[$group->getIdGrupoDemanda()] = array(
            'group' => $group,
            'areasTematicas' => $areasTematicas
        );

        if ($group->getFgTituloSimples() != 1)
            $groups[$group->getIdGrupoDemanda()]['areas'] = $areas;
        else
            $groups[$group->getIdGrupoDemanda()]['options'] = $cedulas;
        
        self::render(compact('step', 'nextURL', 'totalSteps', 'groups', 'qtdMaxEscolha', 'nextStep'));
    }

    public static function review()
    {
        $votingSession = VotingSession::requireCurrentVotingSession();
        $currentUser = $votingSession->requireCurrentUser();
        $regiao = $currentUser->getRegiao();
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

        $areasTematicas = $group->getAreasTematicas($regiao->getIdRegiao());
        $selectedOptions = array();
        foreach ($votes as $vote) {
            $option = $selection[$vote->getIdCedula()];
            $selectedOptions[] = $option;
        }

        $html = new HTMLHelper();
        $lastStep = $votingSession->getLastStep();
        self::setJavascriptVar('previousStep', $lastStep);
        self::setJavascriptVar('previousStepURL', $html->url(array('controller' => 'Election', 'action' => 'step', 'step' => $lastStep)));

        self::render(compact('options', 'votes', 'group', 'selection', 'next_group', 'areasTematicas', 'selectedOptions'));
    }

    public static function confirm()
    {
        $votingSession = VotingSession::requireCurrentVotingSession();
        $currentUser = $votingSession->requireCurrentUser();
        $group = $votingSession->getCurrentGroup();

        self::registerVotes();
        $votes = Vote::getSessionVotes();
        $votoLog = VotoLog::cast($votingSession->getVotoLog());

        if (count($votes) > $group->getQtdMaxEscolha()) {
            $e = new AppException("Excedido o número de " . $group->getQtdMaxEscolha() . " opções.", AppException::ERROR, array('controller' => 'Election', 'action' => 'review'));
            self::flash($e);
            throw $e;
        }

        foreach ($votes as $vote) {
            $voto = new Voto($vote->getIdCedula(), $currentUser->getRegiao()->getIdMunicipio(), $votoLog->getIdMeioVotacao(), $_SERVER['REMOTE_ADDR']);
            $voto->setIdVoto($voto->insert());
        }

        $votoLog->setDthFim(new DateTime());
        $votoLog->setQtdSelecoes(count($votes));
        $votoLog->update();
        $next_group = $group->findNextGroup(TRUE);
        $votingSession->finishGroup();

        if (is_array($next_group) && reset($next_group) instanceof GrupoDemanda)
            self::redirect(array('controller' => 'Election', 'action' => 'start'));
        else {
            //$votingSession->finish();
            self::redirect(array('controller' => 'Election', 'action' => 'success'));
        }
    }

    public static function success()
    {
        $votingSession = VotingSession::requireCurrentVotingSession();
        $currentUser = $votingSession->requireCurrentUser();
        $votacao = Votacao::findMostRecent();
        
        $poll = null;
        if($currentUser->hasPollAvailable($votacao->getIdVotacao()))
            $poll = Poll::findLastByVotacao($votacao->getIdVotacao());
        
        self::setJavascriptVar('previousStep', 0);
        self::addJavascript('/js/poll.js');
        
        self::render(compact("poll"));
    }

    private static function registerVotes()
    {
        if (!self::isPost())
            return;
        $votingSession = VotingSession::requireCurrentVotingSession();
        $group = $votingSession->getCurrentGroup();

        $previousStep = self::getParam('votes_step');
        if (is_null($previousStep))
            return;

        $selected = self::getParam('selected');
        if (is_null($selected))
            $selected = array();

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
