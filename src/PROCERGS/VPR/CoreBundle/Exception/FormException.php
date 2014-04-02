<?php

namespace PROCERGS\VPR\CoreBundle\Exception;

use Symfony\Component\Form\FormError;

class FormException extends \Exception
{

    private $field;
    private $formError;

    /**
     *
     * @param mixed $message message or FormError
     * @param type $field
     */
    public function __construct($message, $field = null)
    {
        if ($message instanceof FormError) {
            $formError = $message;
            $message = $formError->getMessage();
        } else {
            $formError = new FormError($message);
        }
        parent::__construct($message);
        $this->field = $field;
        $this->formError = $formError;
    }

    public function getFormError()
    {
        return $this->formError;
    }

}
