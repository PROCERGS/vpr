<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Entity\Sms;

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
}
