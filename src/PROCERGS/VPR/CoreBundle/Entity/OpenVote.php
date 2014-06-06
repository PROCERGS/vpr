<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OpenVote
 *
 * @ORM\Table(name="open_vote")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\OpenVoteRepository")
 */
class OpenVote
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="BallotBox")
     * @ORM\JoinColumn(name="ballot_box_id", referencedColumnName="id", nullable=false)
     */
    protected $ballotBox;

    /**
     * @ORM\ManyToOne(targetEntity="Corede")
     * @ORM\JoinColumn(name="corede_id", referencedColumnName="id")
     */
    protected $corede;

    /**
     * @ORM\ManyToOne(targetEntity="PollOption")
     * @ORM\JoinColumn(name="poll_option_id", referencedColumnName="id")
     */
    protected $pollOption;

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="text")
     */
    protected $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="auth_type", type="string", length=255)
     */
    protected $authType;

    /**
     * @var string
     *
     * @ORM\Column(name="voter_registration", type="boolean")
     */
    protected $validVoterRegistration;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     */
    protected $city;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set signature
     *
     * @param string $signature
     * @return OpenVote
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set authType
     *
     * @param string $authType
     * @return OpenVote
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;

        return $this;
    }

    /**
     * Get authType
     *
     * @return string
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * Set voterRegistration
     *
     * @param string $voterRegistration
     * @return OpenVote
     */
    public function setVoterRegistration($voterRegistration)
    {
        $this->voterRegistration = $voterRegistration;

        return $this;
    }

    /**
     * Get voterRegistration
     *
     * @return string
     */
    public function getVoterRegistration()
    {
        return $this->voterRegistration;
    }

    public function setCity($var)
    {
        $this->city = $var;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCorede($var)
    {
        $this->corede = $var;

        return $this;
    }

    public function getCorede()
    {
        return $this->corede;
    }
}
