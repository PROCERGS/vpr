<?php

namespace PROCERGS\VPR\CoreBundle\Security;


use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Entity\TREVoterRepository;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Exception\TREVoterException;
use PROCERGS\VPR\CoreBundle\Helper\PollOptionHelper;

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
    protected $smsRegex;

    /**
     * SmsVoteHandler constructor.
     * @param PollOptionHelper $pollOptionHelper
     * @param TREVoterRepository $treVoterRepository
     */
    public function __construct(PollOptionHelper $pollOptionHelper, TREVoterRepository $treVoterRepository)
    {
        $this->pollOptionHelper = $pollOptionHelper;
        $this->treVoterRepository = $treVoterRepository;
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

        /** @var TREVoter $treVoter */
        $treVoter = $this->treVoterRepository->findOneBy(
            [
                'id' => (int)$voterRegistration,
            ]
        );

        if (!($treVoter instanceof TREVoter)) {
            throw new TREVoterException('Voter Registration not found');
        }

        $corede = $treVoter->getCorede();
        $receivedOptions = $parsed[self::MESSAGE_OPTIONS];
        $translatedIds = $this->pollOptionHelper->ballotSeqToIds($receivedOptions, $corede);

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
            self::MESSAGE_VOTER_REGISTRATION => $m[2],
            self::MESSAGE_OPTIONS => $options,
        ];
    }
}
