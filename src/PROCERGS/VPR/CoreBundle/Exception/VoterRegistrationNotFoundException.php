<?php
namespace PROCERGS\VPR\CoreBundle\Exception;

class VoterRegistrationNotFoundException extends TREVoterException
{

    public function __construct($message = 'register.voter_registration.notfound', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
