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
        $to = new PhoneNumber();
        $to
            ->setCountryCode($this->container->getParameter('test.tpd.to_phone.country_code'))
            ->setAreaCode($this->container->getParameter('test.tpd.to_phone.area_code'))
            ->setSubscriberNumber($this->container->getParameter('test.tpd.to_phone.subscriber'));

        $response = $this->sendSms($to, 'sms test');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
        $this->assertTrue(is_string($response));
    }

    public function testForceReceiveAll()
    {
        $tag = $this->container->getParameter('test.tpd.sms_tag');

        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $allSms = $smsService->forceReceive($tag);
        $this->assertNotEmpty($allSms);
        $lastSms = end($allSms);

        $smsQueue = $smsService->forceReceive($tag, $lastSms->id);
        $this->assertEmpty($smsQueue);
    }

    public function testStatus()
    {
        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $to = new PhoneNumber();
        $to
            ->setAreaCode($this->container->getParameter('test.tpd.from_phone.area_code'))
            ->setSubscriberNumber($this->container->getParameter('test.tpd.from_phone.subscriber'));
        $transactionId = $this->sendSms($to, 'testing status');

        $status = $smsService->getStatus($transactionId);
        $this->assertNotNull($status);
        $this->assertNotEmpty($status);

        $first = reset($status);
        $this->assertEquals($transactionId, $first->numero);
    }

    private function sendSms(PhoneNumber $to, $message)
    {
        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $sms = new Sms();
        $sms
            ->setFrom($this->container->getParameter('tpd_system_id'))
            ->setTo($to)
            ->setMessage($message);

        return $smsService->send($sms);
    }
}
