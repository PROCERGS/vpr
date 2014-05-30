<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatsOptionVote
 *
 * @ORM\Table(name="stats_option_vote", indexes={
 *      @ORM\index(name="idx_poll_option_id", columns={"poll_option_id"})
 * })
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\StatsOptionVoteRepository")
 */
class StatsOptionVote
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
     * @ORM\Column(name="poll_option_id", type="integer")
     */
    protected $pollOptionId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="poll_id", type="integer")
     */
    protected $pollId;

    /**
     * @var integer
     *
     * @ORM\Column(name="corede_id", type="integer")
     */
    protected $coredeId;

    /**
     * @var boolean
     * @ORM\Column(name="has_voter_registration", type="boolean", nullable=true)
     */
    protected $hasVoterRegistration;

    /**
     * @var boolean
     * @ORM\Column(name="has_login_cidadao", type="boolean", nullable=true)
     */
    protected $hasLoginCidadao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

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
     * Get pollOptionId
     *
     * @return integer
     */
    public function getPollOptionId()
    {
        return $this->pollOptionId;
    }

    /**
     * Set pollOptionId
     *
     * @param integer $pollOptionId
     * @return StatsOptionVote
     */
    public function setPollOptionId($pollOptionId)
    {
        $this->pollOptionId = $pollOptionId;

        return $this;
    }

    /**
     * Get pollId
     *
     * @return integer
     */
    public function getPollId()
    {
        return $this->pollId;
    }

    /**
     * Set pollId
     *
     * @param integer $pollId
     * @return StatsOptionVote
     */
    public function setPollId($pollId)
    {
        $this->pollId = $pollId;

        return $this;
    }

    /**
     * Get coredeId
     *
     * @return integer
     */
    public function getCoredeId()
    {
        return $this->coredeIdd;
    }

    /**
     * Set coredeId
     *
     * @param integer $coredeId
     * @return StatsOptionVote
     */
    public function setCoredeId($coredeId)
    {
        $this->coredeId = $coredeId;

        return $this;
    }
    
    /**
     * Get hasVoterRegistration
     *
     * @return integer
     */
    public function getHasVoterRegistration()
    {
        return $this->hasVoterRegistration;
    }

    /**
     * Set hasVoterRegistration
     *
     * @param bool $hasVoterRegistration
     * @return StatsOptionVote
     */
    public function setHasVoterRegistration($hasVoterRegistration)
    {
        $this->hasVoterRegistration = $hasVoterRegistration;

        return $this;
    }    
    
    /**
     * Get hasVoterRegistration
     *
     * @return integer
     */
    public function getHasLoginCidadao()
    {
        return $this->hasLoginCidadao;
    }

    /**
     * Set hasLoginCidadao
     *
     * @param bool $hasLoginCidadao
     * @return StatsOptionVote
     */
    public function setHasLoginCidadao($hasLoginCidadao)
    {
        $this->hasLoginCidadao = $hasLoginCidadao;

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
 
}
