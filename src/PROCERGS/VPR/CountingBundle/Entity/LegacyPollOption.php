<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollOption
 *
 * @ORM\Table(name="legacy_poll_option", indexes={@ORM\Index(name="fk_poll_option_poll_question", columns={"poll_question_id"}), @ORM\Index(name="poll_option_cod_pop", columns={"cod_pop"})})
 * @ORM\Entity
 */
class LegacyPollOption
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
     * @ORM\Column(name="poll_question_id", type="integer", nullable=false)
     */
    private $pollQuestionId;

    /**
     * @var string
     *
     * @ORM\Column(name="option", type="text", nullable=false)
     */
    private $option;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=45, nullable=true)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_pop", type="integer", nullable=false)
     */
    private $codPop;



    /**
     * Set pollQuestionId
     *
     * @param integer $pollQuestionId
     * @return PollOption
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
     * Set option
     *
     * @param string $option
     * @return PollOption
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return string 
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return PollOption
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set codPop
     *
     * @param integer $codPop
     * @return PollOption
     */
    public function setCodPop($codPop)
    {
        $this->codPop = $codPop;

        return $this;
    }

    /**
     * Get codPop
     *
     * @return integer 
     */
    public function getCodPop()
    {
        return $this->codPop;
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
