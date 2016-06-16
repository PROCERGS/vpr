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
use PROCERGS\VPR\CoreBundle\Tests\KernelAwareTest;

class SmsVoteHandlerTest extends KernelAwareTest
{
    const VOTER_REGISTRATION = 418551280469;

    /** @var SmsVote */
    protected $validSms;

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
            ->setMessage('VOTE '.self::VOTER_REGISTRATION.' 1 2 3 # 5  9#7  % 6')
            ->setReceivedAt(new \DateTime());

        $poll = new Poll();
        $poll->setName('Test Poll')
            ->setOpeningTime(new \DateTime("-1 hour"))
            ->setClosingTime(new \DateTime("+1 hour"))
            ->setApurationTime(new \DateTime("+2 hours"))
            ->setDescription('Test Poll')
            ->generatePrivateAndPublicKeys();
        $this->pollPassphrase = $poll->getSecret();
        $this->em->persist($poll);
        $this->em->flush($poll);

        $ballotBox = new BallotBox();
        $this->ballotBoxPassphrase = $ballotBox->generatePassphrase();
        $ballotBox->setName('SMS BallotBox')
            ->setPoll($poll)
            ->setSecret($this->ballotBoxPassphrase)
            ->setPin(1234)
            ->setIsOnline(false)
            ->setIsSms(true)
            ->setKeys();
        $this->em->persist($ballotBox);
        $this->em->flush($ballotBox);

        $corede = new Corede();
        $corede->setName('Metropolitano');
        $this->em->persist($corede);
        $this->em->flush($corede);

        $city = new City();
        $city->setName('Porto Alegre')
            ->setId(88013)
            ->setIsCapital(true)
            ->setCodSefa(96)
            ->setIbgeCode(4314902)
            ->setCorede($corede);
        $this->em->persist($city);
        $this->em->flush($city);

        $voterRegistration = self::VOTER_REGISTRATION;
        $treVoter = new TREVoter($voterRegistration);
        $treVoter->setId(self::VOTER_REGISTRATION)
            ->setVotingZone(1)
            ->setName('Fulano de Tal')
            ->setCity($city)
            ->setCorede($corede)
            ->setCityName($city->getName())
            ->setCityCode($city->getId());
        $this->em->persist($treVoter);
        $this->em->flush($treVoter);
    }

    public function testSmsVoteParsing()
    {
        $smsVoteHandler = new SmsVoteHandler();

        $smsVote = $this->validSms;

        $parsed = $smsVoteHandler->parseMessage($smsVote);
        $this->assertEquals('VOTE', $parsed[SmsVoteHandler::MESSAGE_PREFIX]);
        $this->assertEquals(self::VOTER_REGISTRATION, $parsed[SmsVoteHandler::MESSAGE_VOTER_REGISTRATION]);
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

        $smsVoteHandler = new SmsVoteHandler();

        $smsVote = $this->validSms;

        /** @var TREVoterRepository $treVoterRepo */
        $treVoterRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:TREVoter');

        $vote = $smsVoteHandler->getVoteFromSmsVote($smsVote, $ballotBox, $treVoterRepo);
        $this->assertInstanceOf('PROCERGS\VPR\CoreBundle\Entity\Vote', $vote);
        $this->assertNotEmpty($vote->getPollOption(), 'No options found in the vote!');

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
