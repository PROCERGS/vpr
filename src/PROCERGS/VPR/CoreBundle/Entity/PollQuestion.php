<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollQuestion
 *
 * @ORM\Table(name="poll_question", indexes={@ORM\Index(name="fk_poll_question_poll", columns={"poll_id"})})
 * @ORM\Entity
 */
class PollQuestion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    private $sequence;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text", nullable=false)
     */
    private $question;

    /**
     * @var integer
     *
     * @ORM\Column(name="min_selection", type="integer", nullable=false)
     */
    private $minSelection;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_selection", type="integer", nullable=false)
     */
    private $maxSelection;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Poll
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Poll")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poll_id", referencedColumnName="id")
     * })
     */
    private $poll;



    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return PollQuestion
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set question
     *
     * @param string $question
     * @return PollQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set minSelection
     *
     * @param integer $minSelection
     * @return PollQuestion
     */
    public function setMinSelection($minSelection)
    {
        $this->minSelection = $minSelection;

        return $this;
    }

    /**
     * Get minSelection
     *
     * @return integer 
     */
    public function getMinSelection()
    {
        return $this->minSelection;
    }

    /**
     * Set maxSelection
     *
     * @param integer $maxSelection
     * @return PollQuestion
     */
    public function setMaxSelection($maxSelection)
    {
        $this->maxSelection = $maxSelection;

        return $this;
    }

    /**
     * Get maxSelection
     *
     * @return integer 
     */
    public function getMaxSelection()
    {
        return $this->maxSelection;
    }

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
     * Set poll
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Poll $poll
     * @return PollQuestion
     */
    public function setPoll(\PROCERGS\VPR\CoreBundle\Entity\Poll $poll = null)
    {
        $this->poll = $poll;

        return $this;
    }

    /**
     * Get poll
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Poll 
     */
    public function getPoll()
    {
        return $this->poll;
    }
}
