<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Service;


use PROCERGS\VPR\CoreBundle\Entity\Sms\PhoneNumber;
use PROCERGS\VPR\CoreBundle\Entity\Sms\Sms;
use PROCERGS\VPR\CoreBundle\Service\SmsService;
use PROCERGS\VPR\CoreBundle\Tests\KernelAwareTest;

class SmsServiceTest extends KernelAwareTest
{
    public function testSend()
    {
        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $to = new PhoneNumber();
        $to
            ->setCountryCode(55)
            ->setAreaCode(51)
            ->setSubscriberNumber(99674527);

        $sms = new Sms();
        $sms
            ->setFrom($this->container->getParameter('tpd_system_id'))
            ->setTo($to)
            ->setMessage('sms test');

        $response = $smsService->send($sms);
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
        var_dump($response);
        $this->assertTrue(is_string($response));
    }
}
