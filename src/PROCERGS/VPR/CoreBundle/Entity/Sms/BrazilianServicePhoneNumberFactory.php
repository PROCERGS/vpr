<?php

namespace PROCERGS\VPR\CoreBundle\Entity\Sms;

class BrazilianServicePhoneNumberFactory
{
    const COUNTRY_CODE = 55;

    /**
     * Parses a brazilian service E.164 formatted phone number and returns it as PhoneNumber.
     * @param $e164 string Brazilian service E.164 formatted phone number
     * @return PhoneNumber
     */
    public static function createFromE164($e164)
    {
        $regex = sprintf('/\+(%s)(\d{2})(\d{1,9})/', self::COUNTRY_CODE);

        if (false === preg_match($regex, $e164, $m)) {
            throw new \InvalidArgumentException('Invalid phone number');
        }

        $phoneNumber = new PhoneNumber();
        $phoneNumber
            ->setCountryCode($m[1])
            ->setAreaCode($m[2])
            ->setSubscriberNumber($m[3]);

        return $phoneNumber;
    }
}
