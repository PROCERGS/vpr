<?php

class PollAnswers extends Model 
{
    protected static $__schema = 'seplag_vpr';
    
    protected $id;
    protected $poll_question_id;
    protected $poll_option_id;
    protected $municipio_id;

    /**
     * @return PollAnswers
     */
    public static function cast($o)
    {
        return $o;
    }

}
