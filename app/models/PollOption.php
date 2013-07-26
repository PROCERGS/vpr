<?php

class PollOption extends Model 
{
    protected static $__schema = 'seplag_vpr';
    
    protected $id;
    protected $poll_question_id;
    protected $option;
    protected $value;
    protected $cod_pop;

    /**
     * @return PollOption
     */
    public static function cast($o)
    {
        return $o;
    }

    public function getPollQuestion()
    {
        return reset(PollQuestion::findById($this->getPollQuestionId()));
    }    
    
	public static function getSessionPollAnswers() {
		$votingSession = VotingSession::requireCurrentVotingSession();
		$answers = $votingSession->getCurrentPollAnswers();
		if (is_null($answers)) {
			$votingSession->setVotes(array());
		}
		return $answers;
	}    
    
    public function isChecked(){
        $currentPollAnswers = self::getSessionPollAnswers();
        $question_id = $this->getPollQuestionId();
        $checked = false;

        if(is_array($currentPollAnswers[$question_id])){
            $checked = in_array($this->getId(), $currentPollAnswers[$question_id]);
        }

        return $checked;
    }
}
