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

}
