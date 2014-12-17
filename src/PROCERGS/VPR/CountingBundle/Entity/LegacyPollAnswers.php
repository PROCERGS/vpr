<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollAnswers
 *
 * @ORM\Table(name="legacy_poll_answers", indexes={@ORM\Index(name="fk_poll_answers_poll_question", columns={"poll_question_id"}), @ORM\Index(name="fk_poll_answers_poll_option", columns={"poll_option_id"}), @ORM\Index(name="fk_poll_answers_poll_municipio", columns={"municipio_id"}), @ORM\Index(name="fk_poll_answers_poll", columns={"poll_id"})})
 * @ORM\Entity
 */
class LegacyPollAnswers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="poll_id", type="integer", nullable=false)
     */
    private $pollId;

    /**
     * @var integer
     *
     * @ORM\Column(name="poll_question_id", type="integer", nullable=false)
     */
    private $pollQuestionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="poll_option_id", type="integer", nullable=false)
     */
    private $pollOptionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="municipio_id", type="integer", nullable=false)
     */
    private $municipioId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;



    /**
     * Set pollId
     *
     * @param integer $pollId
     * @return PollAnswers
     */
    public function setPollId($pollId)
    {
        $this->pollId = $pollId;

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
     * Set pollQuestionId
     *
     * @param integer $pollQuestionId
     * @return PollAnswers
     */
    public function setPollQuestionId($pollQuestionId)
    {
        $this->pollQuestionId = $pollQuestionId;

        return $this;
    }

    /**
     * Get pollQuestionId
     *
     * @return integer 
     */
    public function getPollQuestionId()
    {
        return $this->pollQuestionId;
    }

    /**
     * Set pollOptionId
     *
     * @param integer $pollOptionId
     * @return PollAnswers
     */
    public function setPollOptionId($pollOptionId)
    {
        $this->pollOptionId = $pollOptionId;

        return $this;
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
     * Set municipioId
     *
     * @param integer $municipioId
     * @return PollAnswers
     */
    public function setMunicipioId($municipioId)
    {
        $this->municipioId = $municipioId;

        return $this;
    }

    /**
     * Get municipioId
     *
     * @return integer 
     */
    public function getMunicipioId()
    {
        return $this->municipioId;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return PollAnswers
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
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
}
