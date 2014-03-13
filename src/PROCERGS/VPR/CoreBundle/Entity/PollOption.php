<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollOption
 *
 * @ORM\Table(name="poll_option", indexes={@ORM\Index(name="fk_poll_option_poll_question", columns={"poll_question_id"})})
 * @ORM\Entity
 */
class PollOption
{
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

    /**
     * Set pollQuestion
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\PollQuestion $pollQuestion
     * @return PollOption
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
}
