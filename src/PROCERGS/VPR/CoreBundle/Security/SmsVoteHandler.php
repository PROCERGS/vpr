<?php

namespace PROCERGS\VPR\CoreBundle\Security;


use Doctrine\ORM\EntityManager;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;
use PROCERGS\VPR\CoreBundle\Entity\Sms\BrazilianPhoneNumberFactory;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVoteRepository;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Entity\TREVoterRepository;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Exception\TREVoterException;
use PROCERGS\VPR\CoreBundle\Helper\PollOptionHelper;
use PROCERGS\VPR\CoreBundle\Service\SmsService;
use PROCERGS\VPR\CoreBundle\Validation\Constraints\VoterRegistrationValidator;

class SmsVoteHandler
{
    const MESSAGE_PREFIX = 'prefix';
    const MESSAGE_VOTER_REGISTRATION = 'voter_registration';
    const MESSAGE_OPTIONS = 'options';

    /** @var PollOptionHelper */
    protected $pollOptionHelper;

    /** @var TREVoterRepository */
    protected $treVoterRepository;

    /** @var VotingSessionProvider */
    protected $votingSessionProvider;

    /** @var string */
    protected $smsPrefix;

    /** @var string */
    protected $smsRegex;

    /**
     * SmsVoteHandler constructor.
     * @param PollOptionHelper $pollOptionHelper
     * @param TREVoterRepository $treVoterRepository
     * @param VotingSessionProvider $votingSessionProvider
     * @param string $smsPrefix
     */
    public function __construct(
        PollOptionHelper $pollOptionHelper,
        TREVoterRepository $treVoterRepository,
        VotingSessionProvider $votingSessionProvider,
        $smsPrefix
    ) {
        $this->pollOptionHelper = $pollOptionHelper;
        $this->treVoterRepository = $treVoterRepository;
        $this->votingSessionProvider = $votingSessionProvider;
        $this->smsPrefix = $smsPrefix;
        $this->smsRegex = '/([A-Za-z]*)\W+(\d+)\W+(.+)/';
    }

    /**
     * Translates an SmsVote to a Vote object
     * @param SmsVote $smsVote
     * @param BallotBox $ballotBox
     * @return Vote
     * @throws TREVoterException
     */
    public function getVoteFromSmsVote(SmsVote $smsVote, BallotBox $ballotBox)
    {
        $parsed = $this->parseMessage($smsVote);
        $voterRegistration = $parsed[self::MESSAGE_VOTER_REGISTRATION];

        if (!VoterRegistrationValidator::isValid($voterRegistration)) {
            throw new TREVoterException('Invalid voter registration: '.$voterRegistration);
        }

        /** @var TREVoter $treVoter */
        $treVoter = $this->treVoterRepository->findOneBy(
            [
                'id' => $voterRegistration,
            ]
        );

        if (!($treVoter instanceof TREVoter)) {
            throw new TREVoterException('Voter registration not found: '.$voterRegistration);
        }

        $poll = $ballotBox->getPoll();
        $corede = $treVoter->getCorede();
        $receivedOptions = $parsed[self::MESSAGE_OPTIONS];
        $translatedIds = $this->pollOptionHelper->ballotSeqToIds($receivedOptions, $poll, $corede);

        $vote = new Vote();
        $vote
            ->setAuthType(Vote::AUTH_VOTER_REGISTRATION)
            ->setBallotBox($ballotBox)
            ->setIpAddress($smsVote->getSender())
            ->setVoterRegistration($voterRegistration)
            ->addPollOption($translatedIds)
            ->setCorede($treVoter->getCorede());

        return $vote;
    }

    /**
     * @param SmsVote $smsVote
     * @return array message components
     * @throws \InvalidArgumentException
     */
    public function parseMessage(SmsVote $smsVote)
    {
        if (false === preg_match($this->smsRegex, $smsVote->getMessage(), $m)
            || !isset($m[3])
        ) {
            throw new \InvalidArgumentException('Invalid vote string');
        }

        $options = explode(' ', preg_replace('/\D+/', ' ', $m[3]));

        return [
            self::MESSAGE_PREFIX => $m[1],
            self::MESSAGE_VOTER_REGISTRATION => str_pad($m[2], 12, '0', STR_PAD_LEFT),
            self::MESSAGE_OPTIONS => $options,
        ];
    }

    public function processPendingSms(
        EntityManager $em,
        SmsService $smsService
    ) {
        /** @var SmsVoteRepository $smsVoteRepository */
        $smsVoteRepository = $em->getRepository('PROCERGSVPRCoreBundle:Sms\SmsVote');
        $lastId = $smsVoteRepository->getLastSmsId();
        $pendingMessages = $smsService->forceReceive($this->smsPrefix, $lastId);

        if (empty($pendingMessages)) {
            return [];
        }

        $ballotBox = $this->votingSessionProvider->getSmsBallotBox();
        $this->votingSessionProvider->setPassphrase($ballotBox->getSecret());

        $votes = [];
        foreach ($pendingMessages as $sms) {
            $to = BrazilianPhoneNumberFactory::createFromE164("+{$sms->de}");
            try {
                $date = \DateTime::createFromFormat('Y-m-d\TH:i:s.000-03:00', $sms->dataHoraRecebimento);
                $smsVote = new SmsVote();
                $smsVote
                    ->setSmsId($sms->id)
                    ->setSender($to->toE164())
                    ->setMessage($sms->mensagem)
                    ->setReceivedAt($date);
                $em->persist($smsVote);
                $em->flush($smsVote);

                $vote = $this->getVoteFromSmsVote($smsVote, $ballotBox);
                $votes[] = $vote;
                $this->votingSessionProvider->persistVote($vote);

                $smsService->easySend($to, "Seu voto foi registrado. Agradecemos a participação.");
            } catch (\InvalidArgumentException $e) {
                continue;
            } catch (TREVoterException $e) {
                $smsService->easySend($to, $e->getMessage());
            }
        }

        return $votes;
    }
}
