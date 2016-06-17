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
        return;
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

    public function testForceReceive()
    {
        $tag = $this->container->getParameter('test.tpd.sms_tag');
        $testMessage = "$tag testing";

        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $to = new PhoneNumber();
        $to
            ->setAreaCode($this->container->getParameter('test.tpd.from_phone.area_code'))
            ->setSubscriberNumber($this->container->getParameter('test.tpd.from_phone.subscriber'));
        $this->sendSms($to, $testMessage);

        $smsQueue = $smsService->forceReceive($tag);
        $this->assertNotEmpty($smsQueue);

        $found = false;
        foreach ($smsQueue as $sms) {
            if ($sms->mensagem == $testMessage) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Test message not found in queue!');

        $smsRepeat = $smsService->forceReceive($tag);
        $this->assertEmpty($smsRepeat, 'Messages are not being removed from the queue!');
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
