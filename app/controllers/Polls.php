<?php
class Polls extends AppController {
	
	protected static function setDefaultJavascripts() {
		parent::setDefaultJavascripts();
		self::addJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
	}

	public static function confirm() {
        if (!self::isPost())
            return;

        $votingSession  = VotingSession::requireCurrentVotingSession();
        $currentUser    = $votingSession->requireCurrentUser();
        $votacao        = Votacao::findMostRecent();
        $poll           = Poll::findLastByVotacao($votacao->getIdVotacao());
        $answers        = $_POST['selected'];

        if($currentUser->hasPollAvailable($votacao->getIdVotacao())) {
            if($answers){
                foreach ($answers as $selected) {
                    $pollAnswer = new PollAnswers();
                    foreach($selected as $i => $item) {
                        $pollOption = reset(PollOption::findById($item));
                        if($pollOption){
                            $maxSelect = $pollOption->getPollQuestion()->getMaxSelection();
                            if($i < $maxSelect) {
                                $pollAnswer->addAnswer($pollOption, $currentUser);
                            }
                        }
                    }
                }
            }

            $pollRespondent = new PollRespondent();
            $pollRespondent->setPollId($poll->getId());
            $pollRespondent->setCidadaoId($currentUser->getIdCidadao());
            $pollRespondent->insert();

            $votingSession->finish();
        }
        
        self::redirect(array('controller' => 'Polls', 'action' => 'success'));
	}
    
    public static function success()
    {
        self::render();
    }

}
