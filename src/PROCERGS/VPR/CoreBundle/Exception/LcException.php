<?php

namespace PROCERGS\VPR\CoreBundle\Exception;

use Symfony\Component\Form\FormError;

class LcException extends \Exception
{

    protected $placeHolder;

    /**
     *
     * @param mixed $message message or FormError
     * @param type $field
     */
    public function __construct($message, $placeholder = null)
    {        
        parent::__construct($message);
        $this->placeHolder = $placeholder;        
    }
    
    public function getPlaceHolder()
    {
        return $this->placeHolder;
    }
}
