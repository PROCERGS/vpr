<?php

class PollQuestion extends Model 
{
    protected static $__schema = 'seplag_vpr';
    
    protected $id;
    protected $poll_id;
    protected $sequence;
    protected $question;
    protected $min_selection;
    protected $max_selection;

    /**
     * @return PollQuestion
     */
    public static function cast($o)
    {
        return $o;
    }

    /**
     * Retorna todas as opções da pergunta
     * @return PollOption[]
     */
    public function getOptions() {
        $query = PDOUtils::getConn()->prepare(PollQuestionQueries::SQL_GET_OPTIONS);
        $poll_question_id = $this->getId();
        $query->execute(compact('poll_question_id'));
        
        return $query->fetchAll(PDO::FETCH_CLASS, 'PollOption');
    }    
    
}
