<?php

namespace PROCERGS\VPR\CoreBundle\Entity\Sms;


class PhoneNumber
{
    protected $countryCode;
    protected $areaCode;
    protected $subscriberNumber;

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     * @return PhoneNumber
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAreaCode()
    {
        return $this->areaCode;
    }

    /**
     * @param mixed $areaCode
     * @return PhoneNumber
     */
    public function setAreaCode($areaCode)
    {
        $this->areaCode = $areaCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscriberNumber()
    {
        return $this->subscriberNumber;
    }

    /**
     * @param mixed $subscriberNumber
     * @return PhoneNumber
     */
    public function setSubscriberNumber($subscriberNumber)
    {
        $this->subscriberNumber = $subscriberNumber;

        return $this;
    }
}