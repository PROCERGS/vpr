<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatsTotalOptionVote
 *
 * @ORM\Table(name="stats_total_option_vote")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\StatsTotalOptionVoteRepository")
 */
class StatsTotalOptionVote
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
     * @ORM\Column(name="option_id", type="integer")
     */
    protected $optionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="option_step_id", type="integer")
     */
    protected $optionStepId;

    /**
     * @var integer
     *
     * @ORM\Column(name="option_number", type="integer")
     */
    protected $optionNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="option_title", type="string", length=255)
     */
    protected $optionTitle;

    /**
     * @var integer
     *
     * @ORM\Column(name="corede_id", type="integer")
     */
    protected $coredeId;

    /**
     * @var decimal
     * @ORM\Column(name="percent", type="decimal", precision=4, scale=4, nullable=true)
     */
    protected $percent;

    /**
     * @var integer
     * @ORM\Column(name="total_votes", type="integer", nullable=true)
     */
    protected $totalVotes;

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
     * Get optionId
     *
     * @return integer
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * Set optionId
     *
     * @param integer $optionId
     * @return StatsTotalOptionVote
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;

        return $this;
    }

    /**
     * Get optionStepId
     *
     * @return integer
     */
    public function getOptionStepId()
    {
        return $this->optionStepId;
    }

    /**
     * Set optionStepId
     *
     * @param integer $optionStepId
     * @return StatsTotalOptionVote
     */
    public function setOptionStepId($optionStepId)
    {
        $this->optionStepId = $optionStepId;

        return $this;
    }

    /**
     * Get optionNumber
     *
     * @return integer
     */
    public function getOptionNumber()
    {
        return $this->optionNumber;
    }

    /**
     * Set optionNumber
     *
     * @param integer $optionNumber
     * @return StatsTotalOptionVote
     */
    public function setOptionNumber($optionNumber)
    {
        $this->optionNumber = $optionNumber;

        return $this;
    }

    /**
     * Get optionTitle
     *
     * @return string
     */
    public function getOptionTitle()
    {
        return $this->optionTitle;
    }

    /**
     * Set optionTitle
     *
     * @param integer $optionTitle
     * @return StatsTotalOptionVote
     */
    public function setOptionTitle($optionTitle)
    {
        $this->optionTitle = $optionTitle;

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
     * @return StatsTotalOptionVote
     */
    public function setCoredeId($coredeId)
    {
        $this->coredeId = $coredeId;

        return $this;
    }

    /**
     * Get percent
     *
     * @return decimal
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set percent
     *
     * @param integer $percent
     * @return StatsTotalOptionVote
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

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
     * @return StatsTotalOptionVote
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

}
