<?php
namespace PROCERGS\VPR\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use PROCERGS\VPR\CoreBundle\Entity\Person;
use Symfony\Component\HttpFoundation\Response;

class PersonEvent extends Event
{

    const VOTER_REGISTRATION_EDIT = 'voter.registration.edit';

    protected $person;

    protected $voterRegistration;

    protected $response;

    public function __construct(Person $person = null, &$voterRegistration = null)
    {
        $this->person = $person;
        $this->voterRegistration = $voterRegistration;
    }

    public function setPerson(Person $var)
    {
        $this->person = $var;
        return $this;
    }

    public function setVoterRegistration($var)
    {
        $this->voterRegistration = $var;
        return $this;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function getVoterRegistration()
    {
        return $this->voterRegistration;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}