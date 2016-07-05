<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Security;

use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\BallotBoxRepository;
use PROCERGS\VPR\CoreBundle\Entity\City;
use PROCERGS\VPR\CoreBundle\Entity\Corede;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\PollRepository;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Entity\TREVoterRepository;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Entity\VoteRepository;
use PROCERGS\VPR\CoreBundle\Security\SmsVoteHandler;
use PROCERGS\VPR\CoreBundle\Security\VotingSessionProvider;
use PROCERGS\VPR\CoreBundle\Tests\DatabasePopulator;
use PROCERGS\VPR\CoreBundle\Tests\KernelAwareTest;

class SmsVoteHandlerTest extends KernelAwareTest
{
    const VOTER_REGISTRATION = 418551280469;

    /** @var SmsVote */
    protected $validSms;

    /** @var SmsVote */
    protected $invalidSms;

    /** @var string */
    protected $ballotBoxPassphrase;

    /** @var string */
    protected $pollPassphrase;

    public function setUp()
    {
        parent::setUp();

        $this->validSms = new SmsVote();
        $this->validSms
            ->setSender('+55 51 1234 56789')
            ->setMessage('VOTE '.self::VOTER_REGISTRATION.' 1 02 # 5  6#3  % 6 test')
            ->setReceivedAt(new \DateTime());

        $this->invalidSms = new SmsVote();
        $this->invalidSms
            ->setSender('+55 51 1234 56789')
            ->setMessage('VOTE#'.self::VOTER_REGISTRATION.'#setel#sdr#seapi#seduc')
            ->setReceivedAt(new \DateTime());

        $populator = new DatabasePopulator($this->em);
        $response = $populator->populate();

        $this->pollPassphrase = $response['pollPassphrase'];
        $this->ballotBoxPassphrase = $response['ballotBoxPassphrase'];

        /** @var City $city */
        $city = $response['city'];

        $populator->addVoter(self::VOTER_REGISTRATION, $city);
    }

    public function testSmsVoteParsing()
    {
        /** @var SmsVoteHandler $smsVoteHandler */
        $smsVoteHandler = $this->container->get('sms.vote_handler');

        $smsVote = $this->validSms;

        $parsed = $smsVoteHandler->parseMessage($smsVote);
        $this->assertEquals('VOTE', $parsed[SmsVoteHandler::MESSAGE_PREFIX]);
        $this->assertEquals(self::VOTER_REGISTRATION, $parsed[SmsVoteHandler::MESSAGE_VOTER_REGISTRATION]);
        $this->assertContains(1, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(2, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(3, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(5, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
        $this->assertContains(6, $parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
    }

    public function testInvalidSmsVoteParsing()
    {
        /** @var SmsVoteHandler $smsVoteHandler */
        $smsVoteHandler = $this->container->get('sms.vote_handler');

        $smsVote = $this->invalidSms;

        $parsed = $smsVoteHandler->parseMessage($smsVote);
        $this->assertEquals('VOTE', $parsed[SmsVoteHandler::MESSAGE_PREFIX]);
        $this->assertEquals(self::VOTER_REGISTRATION, $parsed[SmsVoteHandler::MESSAGE_VOTER_REGISTRATION]);
        $this->assertEmpty($parsed[SmsVoteHandler::MESSAGE_OPTIONS]);
    }

    public function testSmsVoteRegistration()
    {
        /** @var VotingSessionProvider $votingSessionProvider */
        $votingSessionProvider = $this->container->get('vpr_voting_session_provider');
        $votingSessionProvider->setPassphrase($this->ballotBoxPassphrase);

        /** @var PollRepository $pollRepo */
        $pollRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:Poll');

        $poll = $votingSessionProvider->getActivePoll();
        $this->assertInstanceOf('PROCERGS\VPR\CoreBundle\Entity\Poll', $poll);
        $this->assertEquals('Test Poll', $poll->getName());

        /** @var BallotBoxRepository $ballotBoxRepo */
        $ballotBoxRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:BallotBox');
        /** @var BallotBox $ballotBox */
        $ballotBox = $ballotBoxRepo->findOneBy(['isSms' => true, 'poll' => $poll]);

        $this->assertInstanceOf('PROCERGS\VPR\CoreBundle\Entity\BallotBox', $ballotBox);
        $this->assertTrue($ballotBox->isSms());

        /** @var SmsVoteHandler $smsVoteHandler */
        $smsVoteHandler = $this->container->get('sms.vote_handler');

        $smsVote = $this->validSms;

        $vote = $smsVoteHandler->getVoteFromSmsVote($smsVote, $ballotBox);
        $this->assertInstanceOf('PROCERGS\VPR\CoreBundle\Entity\Vote', $vote);
        $this->assertNotEmpty($vote->getPollOption(), 'No options found in the vote!');
        $this->assertContains(10, $vote->getPollOption(), implode(' ', $vote->getPollOption()));
        $this->assertContains(9, $vote->getPollOption());
        $this->assertContains(8, $vote->getPollOption());
        $this->assertContains(6, $vote->getPollOption());
        $this->assertContains(5, $vote->getPollOption());

        $votingSessionProvider->persistVote($vote, null);
        $this->em->flush();

        /** @var VoteRepository $voteRepo */
        $voteRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:Vote');
        $votes = $voteRepo->findBy(['ballotBox' => $ballotBox]);

        $this->assertCount(1, $votes);

        /** @var Vote $savedVote */
        $savedVote = reset($votes);
        $this->assertEquals($ballotBox, $savedVote->getBallotBox());
        $this->assertEquals(self::VOTER_REGISTRATION, $savedVote->getVoterRegistration());
    }
}
