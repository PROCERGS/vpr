<?php

namespace PROCERGS\VPR\CoreBundle\Security;


use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
use PROCERGS\VPR\CoreBundle\Entity\Vote;

class SmsVoteHandler
{
    const MESSAGE_PREFIX = 'prefix';
    const MESSAGE_VOTER_REGISTRATION = 'voter_registration';
    const MESSAGE_OPTIONS = 'options';

    /** @var VotingSessionProvider */
    protected $votingSessionProvider;

    /** @var string */
    protected $smsRegex;

    /**
     * SmsVoteHandler constructor.
     */
    public function __construct()
    {
        $this->smsRegex = '/([A-Za-z]*)\W+(\d+)\W+(.+)/';
    }

    public function registerSmsVote(SmsVote $smsVote, BallotBox $ballotBox)
    {
        $parsed = $this->parseMessage($smsVote);

        $vote = new Vote();
        $vote
            ->setAuthType(Vote::AUTH_VOTER_REGISTRATION)
            ->setBallotBox($ballotBox)
            ->setIpAddress($smsVote->getSender())
            ->setVoterRegistration($parsed[self::MESSAGE_VOTER_REGISTRATION]);
    }

    public function parseMessage(SmsVote $smsVote)
    {
        preg_match($this->smsRegex, $smsVote->getMessage(), $m);

        $options = explode(' ', preg_replace('/\D+/', ' ', $m[3]));

        return [
            self::MESSAGE_PREFIX => $m[1],
            self::MESSAGE_VOTER_REGISTRATION => $m[2],
            self::MESSAGE_OPTIONS => $options,
        ];
    }

    /**
     * @param PollOptionRepository $pollOptionRepository
     * @return array PollOption ids grouped by step
     */
    protected function getOptionsIds(PollOptionRepository $pollOptionRepository)
    {
        $ids = $this->getMessage();
        $options = $pollOptionRepository->findBy();

        return [0];
    }
}
