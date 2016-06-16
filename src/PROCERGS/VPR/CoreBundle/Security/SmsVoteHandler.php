<?php

namespace PROCERGS\VPR\CoreBundle\Security;


use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;
use PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVote;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Entity\TREVoterRepository;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Exception\TREVoterException;

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

    public function getVoteFromSmsVote(SmsVote $smsVote, BallotBox $ballotBox, TREVoterRepository $treVoterRepository)
    {
        $parsed = $this->parseMessage($smsVote);
        $voterRegistration = $parsed[self::MESSAGE_VOTER_REGISTRATION];

        /** @var TREVoter $treVoter */
        $treVoter = $treVoterRepository->findOneBy(
            [
                'id' => (int)$voterRegistration,
            ]
        );

        if (!($treVoter instanceof TREVoter)) {
            throw new TREVoterException('Voter Registration not found');
        }

        $vote = new Vote();
        $vote
            ->setAuthType(Vote::AUTH_VOTER_REGISTRATION)
            ->setBallotBox($ballotBox)
            ->setIpAddress($smsVote->getSender())
            ->setVoterRegistration($voterRegistration)
            ->addPollOption($parsed[self::MESSAGE_OPTIONS])
            ->setCorede($treVoter->getCorede());

        return $vote;
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
}
