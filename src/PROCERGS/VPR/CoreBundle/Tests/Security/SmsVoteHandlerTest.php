<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Security;

use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
use PROCERGS\VPR\CoreBundle\Security\SmsVoteHandler;
use PROCERGS\VPR\CoreBundle\Tests\KernelAwareTest;

class SmsVoteHandlerTest extends KernelAwareTest
{
    protected $validSms;

    public function setUp()
    {
        parent::setUp();

        $this->validSms = new SmsVote();
        $this->validSms
            ->setSender('+55 51 1234 56789')
            ->setMessage('VOTE 6465498465465 1 2 3 # 5  9#7  % 6')
            ->setReceivedAt(new \DateTime());
    }

    public function testSmsVoteParsing()
    {
        $smsVoteHandler = new SmsVoteHandler();

        $smsVote = $this->validSms;

        $parsed = $smsVoteHandler->parseMessage($smsVote);
        $this->assertEquals('VOTE', $parsed[SmsVoteHandler::MESSAGE_PREFIX]);
        $this->assertEquals('6465498465465', $parsed[SmsVoteHandler::MESSAGE_VOTER_REGISTRATION]);
        $this->assertContains(1, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(2, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(3, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(5, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(9, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(7, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(6, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
    }

    public function testSmsVoteRegistration()
    {
        // TODO: find what's the current Poll
        // TODO: get current poll's SMS BallotBox

        /** @var BallotBox $ballotBox */
        $ballotBox = null;
        $smsVoteHandler = new SmsVoteHandler();

        $smsVote = $this->validSms;

        $smsVoteHandler->registerSmsVote($smsVote, $ballotBox);

        $this->assertTrue(false);
    }
}
