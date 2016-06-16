<?php

namespace PROCERGS\VPR\CoreBundle\Exception;


class SmsServiceException extends \Exception
{
    /** @var array */
    protected $errorResponse;

    public function __construct(array $errorResponse)
    {
        $this->errorResponse = $errorResponse;
        if (count($errorResponse) === 1) {
            $error = reset($errorResponse);
            $message = property_exists($error, 'message') ? $error->message : 'Unknown error';
            $details = property_exists($error, 'detail') ? $error->detail : 'No details informed';
            $this->message = sprintf("%s: %s", $message, $details);
        }
    }

    /**
     * @return array
     */
    public function getErrorResponse()
    {
        return $this->errorResponse;
    }

    /**
     * @param array $errorResponse
     * @return SmsServiceException
     */
    public function setErrorResponse($errorResponse)
    {
        $this->errorResponse = $errorResponse;

        return $this;
    }
}
