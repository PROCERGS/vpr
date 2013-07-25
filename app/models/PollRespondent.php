<?php

class PollRespondent extends Model 
{
    protected static $__schema = 'seplag_vpr';
    
    protected $id;
    protected $poll_id;
    protected $cidadao_id;

    /**
     * @return PollRespondent
     */
    public static function cast($o)
    {
        return $o;
    }

}
