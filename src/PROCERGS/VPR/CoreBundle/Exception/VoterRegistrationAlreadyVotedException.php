<?php

namespace PROCERGS\VPR\CoreBundle\Exception;

class VoterRegistrationAlreadyVotedException extends \Exception
{

    public function __construct($message, $code, $previous)
    {
        parent::__construct($message, $code, $previous);
    }

}
