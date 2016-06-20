<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Entity\Sms;

use PROCERGS\VPR\CoreBundle\Entity\Sms\BrazilianPhoneNumberFactory;
use PROCERGS\VPR\CoreBundle\Entity\Sms\BrazilianServicePhoneNumberFactory;
use PROCERGS\VPR\CoreBundle\Entity\Sms\PhoneNumber;

class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testToE164()
    {
        $phone = new PhoneNumber();
        $phone->setCountryCode(1)
            ->setAreaCode(123)
            ->setSubscriberNumber(45678901234);

        $this->assertEquals('+112345678901234', $phone->toE164());
        $this->assertLessThanOrEqual(16, strlen($phone->toE164()));
    }

    public function testNoAreaToE164()
    {
        $phone = new PhoneNumber();
        $phone->setCountryCode(55)
            ->setSubscriberNumber(45678901234);

        $this->assertEquals('+5545678901234', $phone->toE164());
        $this->assertLessThanOrEqual(16, strlen($phone->toE164()));
    }

    public function testE164toPhoneNumber()
    {
        $phone1 = BrazilianPhoneNumberFactory::createFromE164('+555112345678');
        $phone2 = BrazilianPhoneNumberFactory::createFromE164('+5551123456789');
        $phone3 = BrazilianServicePhoneNumberFactory::createFromE164('+5512345');

        $this->assertPhoneNumber($phone1, 55, 51, 12345678);
        $this->assertPhoneNumber($phone2, 55, 51, 123456789);
        $this->assertPhoneNumber($phone3, 55, 12, 345);
    }

    /**
     * @param PhoneNumber $phone
     * @param int $countryCode
     * @param int $areaCode
     * @param int $subscriber
     */
    private function assertPhoneNumber($phone, $countryCode = null, $areaCode = null, $subscriber = null)
    {
        $this->assertInstanceOf('PROCERGS\VPR\CoreBundle\Entity\Sms\PhoneNumber', $phone);

        if ($countryCode !== null) {
            $this->assertEquals($countryCode, $phone->getCountryCode());
        }
        if ($areaCode !== null) {
            $this->assertEquals($areaCode, $phone->getAreaCode());
        }
        if ($subscriber !== null) {
            $this->assertEquals($subscriber, $phone->getSubscriberNumber());
        }
    }
}
