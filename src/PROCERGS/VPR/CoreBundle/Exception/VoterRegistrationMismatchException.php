<?php
namespace PROCERGS\VPR\CoreBundle\Exception;

class VoterRegistrationMismatchException extends TREVoterException
{

    public function __construct($message = 'register.voter_registration.mismatch', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
