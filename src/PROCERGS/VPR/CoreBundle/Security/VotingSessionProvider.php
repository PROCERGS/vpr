<?php

namespace PROCERGS\VPR\CoreBundle\Security;

use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;
use Symfony\Component\HttpFoundation\RequestStack;
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
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;

class VotingSessionProvider
{
    /** @var SessionInterface */
    private $session;

    /** @var EntityManagerInterface */
    private $em;

    /** @var Serializer */
    private $serializer;

    /** @var RequestStack */
    private $requestStack;

    /** @var string */
    private $passphrase;

    public function __construct(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        Serializer $serializer,
        RequestStack $requestStack,
        $passphrase
    ) {
        $this->em = $entityManager;
        $this->session = $session;
        $this->serializer = $serializer;
        $this->passphrase = $passphrase;
        $this->requestStack = $requestStack;
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

    /**
     * Checks if a given person has already cast a vote in the specified BallotBox.
     * @param \PROCERGS\VPR\CoreBundle\Entity\Person $person
     * @param \PROCERGS\VPR\CoreBundle\Entity\BallotBox $ballotBox
     * @param \PROCERGS\VPR\CoreBundle\Entity\Vote $conflictingVote (optional) the conflicting/current vote
     * @return boolean
     * @throws AccessDeniedHttpException
     * @throws VoterAlreadyVotedException
     * @throws VoterRegistrationAlreadyVotedException
     */
    public function checkExistingVotes(
        Person $person,
        BallotBox $ballotBox,
        Vote $conflictingVote = null
    ) {
        $filter = compact('ballotBox');
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
                if ($conflictingVote instanceof Vote && $vote->getId() === $conflictingVote->getId()) {
                    continue;
                }
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
        if (!$this->checkExistingVotes($person, $this->getOnlineBallotBox())) {
            return;
        }

        return $this->save($this->createVotingSession($person));
    }

    public function getNextStep()
    {
        return $this->getVote()->getLastStep();
    }

    /**
     *
     * @param Person $person
     * @param BallotBox $ballotBox
     * @return Vote
     * @throws VotingTimeoutException
     */
    public function createVotingSession(Person $person, $ballotBox = null)
    {
        if (null === $ballotBox) {
            $ballotBox = $this->getOnlineBallotBox();
            if (!$ballotBox) {
                throw new VotingTimeoutException();
            }
        }
        $vote = new Vote();
        $vote->setAuthType(
            $person->getLoginCidadaoAccessToken() ? Vote::AUTH_LOGIN_CIDADAO
                : Vote::AUTH_VOTER_REGISTRATION
        );
        $vote->setBallotBox($ballotBox);
        $corede = $this->em->getRepository('PROCERGSVPRCoreBundle:Corede')
            ->find($person->getCorede()->getId());
        $this->em->refresh($corede);
        $vote->setCorede($corede);
        $vote->setIpAddress($this->requestStack->getMasterRequest()->getClientIp());
        if ($person->getTreVoter() instanceof TREVoter) {
            $vote->setVoterRegistration($person->getTreVoter()->getId());
        }
        if ($person->getLoginCidadaoId()) {
            $vote->setLoginCidadaoId($person->getLoginCidadaoId());
        }
        $badges = $person->getBadges();
        if (strlen($person->getFirstName()) && isset($badges['login-cidadao.valid_email'])
            && $badges['login-cidadao.valid_email'] && isset($badges['login-cidadao.nfg_access_lvl'])
            && $badges['login-cidadao.nfg_access_lvl'] >= 2 && isset($badges['login-cidadao.voter_registration'])
            && $badges['login-cidadao.voter_registration']
        ) {
            $vote->setNfgCpf(1);
        }
        $stepRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:Step');
        $vote->setLastStep($stepRepo->findNextPollStep($vote));

        return $vote;
    }

    public function save($vote)
    {
        $this->em->detach($vote);
        $this->updateVote($vote);

        return $vote;
    }

    public function flush()
    {
        $this->updateVote(null);
        $this->session->remove('vote');
    }

    /**
     * @return Vote
     * @throws AccessDeniedHttpException
     */
    public function requireVotingSession()
    {
        if ($this->hasVotingSession()) {
            return $this->getVote();
        } else {
            throw new AccessDeniedHttpException();
        }
    }

    /**
     * @return Vote
     * @throws AccessDeniedHttpException
     */
    public function requireLastStep()
    {
        $vote = $this->requireVotingSession();
        if ($vote->getLastStep()) {
            return $vote;
        } else {
            throw new AccessDeniedHttpException();
        }
    }

    public function persistVote(Vote $vote, Person $person = null)
    {
        /** @var PollOptionRepository $pollOptionRepo */
        $pollOptionRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $serializer = $this->serializer;
        $context = SerializationContext::create()
            ->setSerializeNull(true)
            ->setGroups(array('vote'));
        $options = $pollOptionRepo->getPollOption($vote);
        $serializedOptions = $serializer->serialize($options, 'json', $context);
        $vote->setPlainOptions($serializedOptions);
        $vote->close($this->passphrase);
        $vote->setCreatedAtValue();

        if ($vote->getBallotBox()) {
            $vote->setBallotBox($this->em->merge($vote->getBallotBox()));
            $this->em->refresh($vote->getBallotBox());
            $this->em->refresh($vote->getBallotBox()->getPoll());
        }
        if ($vote->getCity()) {
            $vote->setCity($this->em->merge($vote->getCity()));
            $this->em->refresh($vote->getCity());
        }
        if ($vote->getCorede()) {
            $vote->setCorede($this->em->merge($vote->getCorede()));
            $this->em->refresh($vote->getCorede());
        }
        if ($vote->getLastStep()) {
            $vote->setLastStep($this->em->merge($vote->getLastStep()));
            $this->em->refresh($vote->getLastStep());
        }

        $this->em->persist($vote);
    }

    public function updateVote(Vote $vote = null)
    {
        $this->session->set('vote', $vote);
    }

    public function setPassphrase($passphrase)
    {
        $this->passphrase = $passphrase;
    }
}
