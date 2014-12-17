<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatsTotalCoredeVote
 *
 * @ORM\Table(name="stats_total_corede_vote")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\StatsTotalCoredeVoteRepository")
 */
class StatsTotalCoredeVote
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
     * @var integer
     *
     * @ORM\Column(name="ballot_box_id", type="integer")
     */
    protected $ballotBoxId;

    /**
     * @var integer
     *
     * @ORM\Column(name="corede_id", type="integer")
     */
    protected $coredeId;

    /**
     * @var string
     *
     * @ORM\Column(name="corede_name", type="string", length=255)
     */
    protected $coredeName;

    /**
     * @var integer
     * @ORM\Column(name="total_with_voter_registration", type="integer")
     */
    protected $totalWithVoterRegistration;

    /**
     * @var integer
     * @ORM\Column(name="total_with_login_cidadao", type="integer", nullable=true)
     */
    protected $totalWithLoginCidadao;

    /**
     * @var integer
     * @ORM\Column(name="total_with_voter_registration_and_login_cidadao", type="integer")
     */
    protected $totalWithVoterRegistrationAndLoginCidadao;

    /**
     * @var integer
     * @ORM\Column(name="total_votes", type="integer")
     */
    protected $totalVotes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=7, nullable=true)
     */
    protected $latitude;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=7, nullable=true)
     */
    protected $longitude;    

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
     * Get ballotBoxId
     *
     * @return integer
     */
    public function getBallotBoxId()
    {
        return $this->ballotBoxId;
    }

    /**
     * Set ballotBoxId
     *
     * @param integer $ballotBoxId
     * @return StatsTotalCoredeVote
     */
    public function setBallotBoxId($ballotBoxId)
    {
        $this->ballotBoxId = $ballotBoxId;

        return $this;
    }

    /**
     * Get coredeId
     *
     * @return integer
     */
    public function getCoredeId()
    {
        return $this->coredeId;
    }

    /**
     * Set coredeId
     *
     * @param integer $coredeId
     * @return StatsTotalCoredeVote
     */
    public function setCoredeId($coredeId)
    {
        $this->coredeId = $coredeId;

        return $this;
    }

    /**
     * Get coredeName
     *
     * @return string
     */
    public function getCoredeName()
    {
        return $this->coredeName;
    }

    /**
     * Set coredeName
     *
     * @param string $coredeName
     * @return StatsTotalCoredeVote
     */
    public function setCoredeName($coredeName)
    {
        $this->coredeName = $coredeName;

        return $this;
    }

    /**
     * Get totalWithVoterRegistration
     *
     * @return integer
     */
    public function getTotalWithVoterRegistration()
    {
        return $this->totalWithVoterRegistration;
    }

    /**
     * Set totalWithVoterRegistration
     *
     * @param integer $totalWithVoterRegistration
     * @return StatsTotalCoredeVote
     */
    public function setTotalWithVoterRegistration($totalWithVoterRegistration)
    {
        $this->totalWithVoterRegistration = $totalWithVoterRegistration;

        return $this;
    }

    /**
     * Get totalWithLoginCidadao
     *
     * @return integer
     */
    public function getTotalWithLoginCidadao()
    {
        return $this->totalWithLoginCidadao;
    }

    /**
     * Set totalWithLoginCidadao
     *
     * @param integer $totalWithLoginCidadao
     * @return StatsTotalCoredeVote
     */
    public function setTotalWithLoginCidadao($totalWithLoginCidadao)
    {
        $this->totalWithLoginCidadao = $totalWithLoginCidadao;

        return $this;
    }

    /**
     * Get totalWithVoterRegistrationAndLoginCidadao
     *
     * @return integer
     */
    public function getTotalWithVoterRegistrationAndLoginCidadao()
    {
        return $this->totalWithVoterRegistrationAndLoginCidadao;
    }

    /**
     * Set totalWithVoterRegistrationAndLoginCidadao
     *
     * @param integer $totalWithVoterRegistration
     * @return StatsTotalCoredeVote
     */
    public function setTotalWithVoterRegistrationAndLoginCidadao($totalWithVoterRegistrationAndLoginCidadao)
    {
        $this->totalWithVoterRegistrationAndLoginCidadao = $totalWithVoterRegistrationAndLoginCidadao;

        return $this;
    }

    /**
     * Get totalVotes
     *
     * @return integer
     */
    public function getTotalVotes()
    {
        return $this->totalVotes;
    }

    /**
     * Set totalVotes
     *
     * @param integer $totalVotes
     * @return StatsTotalCoredeVote
     */
    public function setTotalVotes($totalVotes)
    {
        $this->totalVotes = $totalVotes;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function setLatitude($var)
    {
        $this->latitude = $var;
    
        return $this;
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }
    
    public function setLongitude($var)
    {
        $this->longitude = $var;
    
        return $this;
    }
    
    public function getLongitude()
    {
        return $this->longitude;
    }
    

}
