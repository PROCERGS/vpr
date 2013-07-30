<?php
class Polls extends AppController {

    public static function before()
    {
        parent::before();
        $votacao = Votacao::findMostRecent();
        self::setPageName("Votação de Prioridades - Orçamento " . $votacao->getIntExercicio());
    }
    
	protected static function setDefaultJavascripts() 
    {
		parent::setDefaultJavascripts();
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
	}

	public static function confirm()
    {
        if (!self::isPost())
            return;

        $votingSession  = VotingSession::requireCurrentVotingSession();
        $currentUser    = $votingSession->requireCurrentUser();
        $poll           = $votingSession->getPoll();
        $pollAnswers    = $_POST['selected'];

        $votingSession->setCurrentPollAnswers($pollAnswers);

        if($currentUser->hasPollAvailable($poll->getVotacaoId())) {
            $errors = $poll->validate($pollAnswers);
            if(!$errors){

                if($pollAnswers){
                    foreach ($pollAnswers as $selected) {
                        $pollAnswer = new PollAnswers();
                        foreach($selected as $i => $item) {
                            $pollOption = reset(PollOption::findById($item));
                            if($pollOption){
                                $pollAnswer->addAnswer($pollOption, $currentUser);
                            }
                        }
                    }
                }

                $pollRespondent = new PollRespondent();
                $pollRespondent->setPollId($poll->getId());
                $pollRespondent->setCidadaoId($currentUser->getIdCidadao());
                $pollRespondent->insert();

                $votingSession->finish();

            }else{
                Session::set('poll_errors', $errors);
                self::redirect(array('controller' => 'Election', 'action' => 'success'));
            }
        }

        self::redirect(array('controller' => 'Polls', 'action' => 'success'));
	}

    public static function success()
    {
        self::render();
    }

}
