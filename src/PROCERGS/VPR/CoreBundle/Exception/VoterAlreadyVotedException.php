<?php

namespace PROCERGS\VPR\CoreBundle\Exception;

class VoterAlreadyVotedException extends VoterRegistrationAlreadyVotedException
{

    public function __construct($message, $code, $previous)
    {
        parent::__construct($message, $code, $previous);
    }

}
