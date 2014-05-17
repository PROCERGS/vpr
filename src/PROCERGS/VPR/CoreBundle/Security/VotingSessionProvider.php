<?php

namespace PROCERGS\VPR\CoreBundle\Security;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Doctrine\ORM\EntityManagerInterface;
use PROCERGS\VPR\CoreBundle\Exception\VotingTimeoutException;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationAlreadyVotedException;
use PROCERGS\VPR\CoreBundle\Exception\VoterAlreadyVotedException;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\Person;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Entity\Vote;

class VotingSessionProvider
{

    private $session;
    private $em;

    public function __construct(EntityManagerInterface $entityManager,
                                SessionInterface $session)
    {
        $this->em = $entityManager;
        $this->session = $session;
    }

    /**
     * @return boolean
     */
    public function hasVotingSession()
    {
        return $this->getVote() instanceof Vote;
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\Poll
     */
    public function getActivePoll()
    {
        return $this->em->getRepository('PROCERGSVPRCoreBundle:Poll')->findActivePoll();
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function getVote()
    {
        return $this->session->get('vote');
    }

    public function getActivePollOrFail()
    {
        $poll = $this->getActivePoll();
        if (!($poll instanceof Poll) || $poll->getClosingTime() < new \DateTime()) {
            throw new VotingTimeoutException();
            //return $this->redirect($this->generateUrl('procergsvpr_core_voting_timeout'));
        }
        return $poll;
    }

    public function getOnlineBallotBox($poll = null)
    {
        if (is_null($poll)) {
            $poll = $this->getActivePollOrFail();
        }
        return $this->em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->findOnlineByPoll($poll);
    }

    public function checkExistingVotes(Person $person, &$ballotBox = null)
    {
        if (null === $ballotBox) {
            $ballotBox = $this->getOnlineBallotBox();
        }
        $filter['ballotBox'] = $ballotBox;
        $voteRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:Vote');
        if ($person->getTreVoter() instanceof TREVoter) {
            $filter['voterRegistration'] = $person->getTreVoter()->getId();
        } elseif ($person->getLoginCidadaoId()) {
            $filter['loginCidadaoId'] = $person->getLoginCidadaoId();
        } else {
            throw new AccessDeniedHttpException('Invalid voter');
        }

        $votes = $voteRepo->findBy($filter);
        if (!empty($votes)) {
            foreach ($votes as $vote) {
                if ($vote->getNfgCpf()) {
                    throw new VoterAlreadyVotedException();
                }
            }
            throw new VoterRegistrationAlreadyVotedException();
        }

        return true;
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function enforceVotingSession(Person $person)
    {
        if ($this->hasVotingSession()) {
            return $this->getVote();
        }
        $vote = $this->createVotingSession($person);
        $this->em->detach($vote);
        $this->session->set('vote', $vote);

        return $vote;
    }

    public function getNextStep()
    {
        return $this->getVote()->getLastStep();
    }

    private function createVotingSession(Person $person)
    {
        if (!$this->checkExistingVotes($person, $ball)) {
            return;
        }

        $vote = new Vote();
        $vote->setAuthType($person->getLoginCidadaoAccessToken() ? Vote::AUTH_LOGIN_CIDADAO : Vote::AUTH_VOTER_REGISTRATION);
        $vote->setBallotBox($ball);
        $vote->setCorede($person->getCity()->getCorede());
        $vote->setSmId(uniqid(mt_rand(), true));
        if ($person->getTreVoter() instanceof TREVoter) {
            $vote->setVoterRegistration($person->getTreVoter()->getId());
        }
        if ($person->getLoginCidadaoId()) {
            $vote->setLoginCidadaoId($person->getLoginCidadaoId());
        }
        $pollOptionRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $vote->setLastStep($pollOptionRepo->getNextPollStep($vote));
        return $vote;
    }

    public function flush()
    {
        $this->session->set('vote', null);
        $this->session->remove('vote');
    }
}
