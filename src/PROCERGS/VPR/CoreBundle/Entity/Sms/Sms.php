<?php

namespace PROCERGS\VPR\CoreBundle\Entity\Sms;


class Sms
{
    /** @var PhoneNumber */
    protected $to;

    /** @var PhoneNumber */
    protected $from;

    /** @var string */
    protected $message;

    /**
     * @return PhoneNumber
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param PhoneNumber $to
     * @return Sms
     */
    public function setTo(PhoneNumber $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return PhoneNumber
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param PhoneNumber $from
     * @return Sms
     */
    public function setFrom(PhoneNumber $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return Sms
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
