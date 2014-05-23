<?php
namespace PROCERGS\VPR\CoreBundle\EventListener;

use PROCERGS\VPR\CoreBundle\Event\PersonEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use PROCERGS\VPR\CoreBundle\Entity\Person;
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
        if (is_null($event->getVoterRegistration()) || !strlen($event->getVoterRegistration())) {
            return false;
        }
        $user = $event->getPerson();
        $treRepo = $this->em->getRepository('PROCERGSVPRCoreBundle:TREVoter');
        $voter = $treRepo->findOneBy(array(
            'id' => $event->getVoterRegistration()
        ));
        if ($voter) {
            if (!Person::checkNamesEqual($user->getFirstName(), $voter->getName())) {
                throw new VoterRegistrationMismatchException();
            }
        } else {
            throw new VoterRegistrationNotFoundException();
        }
        $user->setTreVoter($voter);
    }
}