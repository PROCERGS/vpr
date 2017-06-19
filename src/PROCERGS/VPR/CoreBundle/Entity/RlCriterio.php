<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RlCriterio
 *
 * @ORM\Table(name="rl_criterio")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\RlCriterioRepository") 
 */
class RlCriterio
{
    /**
     * @var string
     *
     * @ORM\Column(name="tot_value", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $totValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="tot_program", type="integer", nullable=true)
     */
    private $totProgram;

    /**
     * @var string
     *
     * @ORM\Column(name="program1", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $program1;

    /**
     * @var string
     *
     * @ORM\Column(name="program2", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $program2;

    /**
     * @var string
     *
     * @ORM\Column(name="program3", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $program3;

    /**
     * @var string
     *
     * @ORM\Column(name="program4", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $program4;
    /**
     * @var string
     *
     * @ORM\Column(name="program5", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $program5;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="rl_criterio_id_seq", allocationSize=1, initialValue=1)
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
     * @var \PROCERGS\VPR\CoreBundle\Entity\Corede
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Corede")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="corede_id", referencedColumnName="id")
     * })
     */
    private $corede;



    /**
     * Set totValue
     *
     * @param string $totValue
     * @return RlCriterio
     */
    public function setTotValue($totValue)
    {
        $this->totValue = $totValue;

        return $this;
    }

    /**
     * Get totValue
     *
     * @return string 
     */
    public function getTotValue()
    {
        return $this->totValue;
    }

    /**
     * Set totProgram
     *
     * @param integer $totProgram
     * @return RlCriterio
     */
    public function setTotProgram($totProgram)
    {
        $this->totProgram = $totProgram;

        return $this;
    }

    /**
     * Get totProgram
     *
     * @return integer 
     */
    public function getTotProgram()
    {
        return $this->totProgram;
    }

    /**
     * Set program1
     *
     * @param string $program1
     * @return RlCriterio
     */
    public function setProgram1($program1)
    {
        $this->program1 = $program1;

        return $this;
    }

    /**
     * Get program1
     *
     * @return string 
     */
    public function getProgram1()
    {
        return $this->program1;
    }

    /**
     * Set program2
     *
     * @param string $program2
     * @return RlCriterio
     */
    public function setProgram2($program2)
    {
        $this->program2 = $program2;

        return $this;
    }

    /**
     * Get program2
     *
     * @return string 
     */
    public function getProgram2()
    {
        return $this->program2;
    }

    /**
     * Set program3
     *
     * @param string $program3
     * @return RlCriterio
     */
    public function setProgram3($program3)
    {
        $this->program3 = $program3;

        return $this;
    }

    /**
     * Get program3
     *
     * @return string 
     */
    public function getProgram3()
    {
        return $this->program3;
    }

    /**
     * Set program4
     *
     * @param string $program4
     * @return RlCriterio
     */
    public function setProgram4($program4)
    {
        $this->program4 = $program4;

        return $this;
    }

    /**
     * Get program4
     *
     * @return string 
     */
    public function getProgram4()
    {
        return $this->program4;
    }
    
    /**
     * Set program5
     *
     * @param string $program5
     * @return RlCriterio
     */
    public function setProgram5($program5)
    {
        $this->program5 = $program5;
    
        return $this;
    }
    
    /**
     * Get program5
     *
     * @return string
     */
    public function getProgram5()
    {
        return $this->program5;
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
     * @return RlCriterio
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

    /**
     * Set corede
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Corede $corede
     * @return RlCriterio
     */
    public function setCorede(\PROCERGS\VPR\CoreBundle\Entity\Corede $corede = null)
    {
        $this->corede = $corede;

        return $this;
    }

    /**
     * Get corede
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Corede 
     */
    public function getCorede()
    {
        return $this->corede;
    }
}
