<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OpenVote
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\Column(name="signature", type="string", length=255)
     */
    protected $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="authType", type="string", length=255)
     */
    protected $authType;

    /**
     * @var string
     *
     * @ORM\Column(name="voterRegistration", type="boolean")
     */
    protected $validVoterRegistration;


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
}
