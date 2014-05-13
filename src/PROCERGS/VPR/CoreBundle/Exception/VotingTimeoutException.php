<?php

namespace PROCERGS\VPR\CoreBundle\Exception;

class VotingTimeoutException extends \Exception
{

    public function __construct($message, $code, $previous)
    {
        parent::__construct($message, $code, $previous);
    }

}
