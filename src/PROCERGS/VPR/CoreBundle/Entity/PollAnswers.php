<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollAnswers
 *
 * @ORM\Table(name="poll_answers", indexes={@ORM\Index(name="fk_poll_answers_poll_question", columns={"poll_question_id"}), @ORM\Index(name="fk_poll_answers_poll_option", columns={"poll_option_id"}), @ORM\Index(name="fk_poll_answers_poll_municipio", columns={"municipio_id"}), @ORM\Index(name="fk_poll_answers_poll", columns={"poll_id"})})
 * @ORM\Entity
 */
class PollAnswers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\PollQuestion
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\PollQuestion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poll_question_id", referencedColumnName="id")
     * })
     */
    private $pollQuestion;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\PollOption
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\PollOption")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poll_option_id", referencedColumnName="id")
     * })
     */
    private $pollOption;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Municipio
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio_id", referencedColumnName="id_municipio")
     * })
     */
    private $municipio;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pollQuestion
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\PollQuestion $pollQuestion
     * @return PollAnswers
     */
    public function setPollQuestion(\PROCERGS\VPR\CoreBundle\Entity\PollQuestion $pollQuestion = null)
    {
        $this->pollQuestion = $pollQuestion;

        return $this;
    }

    /**
     * Get pollQuestion
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\PollQuestion 
     */
    public function getPollQuestion()
    {
        return $this->pollQuestion;
    }

    /**
     * Set pollOption
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\PollOption $pollOption
     * @return PollAnswers
     */
    public function setPollOption(\PROCERGS\VPR\CoreBundle\Entity\PollOption $pollOption = null)
    {
        $this->pollOption = $pollOption;

        return $this;
    }

    /**
     * Get pollOption
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\PollOption 
     */
    public function getPollOption()
    {
        return $this->pollOption;
    }

    /**
     * Set municipio
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Municipio $municipio
     * @return PollAnswers
     */
    public function setMunicipio(\PROCERGS\VPR\CoreBundle\Entity\Municipio $municipio = null)
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Municipio 
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Set poll
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Poll $poll
     * @return PollAnswers
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
