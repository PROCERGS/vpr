<?php

namespace PROCERGS\VPR\CoreBundle\Exception;

class OpenSSLException extends \Exception
{

    public function __construct($message = null, $code = null, $previous = null)
    {
        if (is_null($message)) {
            $message = openssl_error_string();
        }
        parent::__construct($message, $code, $previous);
    }

}
