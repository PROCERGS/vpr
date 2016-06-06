<?php

namespace PROCERGS\VPR\CoreBundle\Exception\OIDC;

class UserNotFoundException extends \RuntimeException
{
    public function __construct($message, $code = 403, \Exception $previous)
    {
        parent::__construct($message, $code, $previous);
    }
}
