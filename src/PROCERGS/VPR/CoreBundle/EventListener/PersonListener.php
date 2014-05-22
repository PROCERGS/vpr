<?php
namespace PROCERGS\VPR\CoreBundle\EventListener;

use PROCERGS\VPR\CoreBundle\Event\PersonEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationMismatchException;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationNotFoundException;

class PersonListener implements EventSubscriberInterface
{

    protected $em;

    public function setEntityManager(EntityManager $var)
    {
        $this->em = $var;
    }

    public static function getSubscribedEvents()
    {
        return array(
            PersonEvent::VOTER_REGISTRATION_EDIT => 'onVoterRegistraionEdit'
        );
    }

    public function onVoterRegistraionEdit(PersonEvent $event)
    {
        if (null == $event->getVoterRegistration() || !strlen($event->getVoterRegistration())) {
            return false;
        }
        $user = $event->getPerson();
        $treRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:TREVoter');
        $voter = $treRepo->findOneBy(array(
            'id' => $event->getVoterRegistration()
        ));
        if ($voter) {
            $n1 = mb_strtolower(substr($user->getFirstName(), 0, strpos($user->getFirstName(), ' ')));
            $n2 = mb_strtolower(substr($voter->getName(), 0, strpos($voter->getName(), ' ')));
            if ($n1 != $n2) {
                throw new VoterRegistrationMismatchException();
            }
        } else {
            throw new VoterRegistrationNotFoundException();
        }
        $user->setTreVoter($voter);
        $user->setCity($voter->getCity());
    }
}