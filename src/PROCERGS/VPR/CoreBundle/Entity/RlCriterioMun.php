<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RlCriterioMun
 *
 * @ORM\Table(name="rl_criterio_mun")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\RlCriterioMunRepository")
 */
class RlCriterioMun
{
    const CALC_POPULATION = 1;
    const CALC_PROGRAM = 2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="calc_type", type="integer", nullable=false)
     */
    private $calcType;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="limit_citizen", type="integer", nullable=true)
     */
    private $limitCitizen;

    /**
     * @var string
     *
     * @ORM\Column(name="perc_apply", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $percApply;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="rl_criterio_mun_id_seq", allocationSize=1, initialValue=1)
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
     * @return RlCriterioMun
     */
    public function setLimitCitizen($val)
    {
        $this->limitCitizen = $val;

        return $this;
    }

    /**
     * @return string 
     */
    public function getLimitCitizen()
    {
        return $this->limitCitizen;
    }

    /**
     * @return RlCriterioMun
     */
    public function setPercApply($val)
    {
        $this->percApply = $val;

        return $this;
    }

    /**
     * @return string 
     */
    public function getPercApply()
    {
        return $this->percApply;
    }
    
    /**
     * @return RlCriterioMun
     */
    public function setCalcType($val)
    {
        $this->calcType = $val;
    
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCalcType()
    {
        return $this->calcType;
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
     * @return RlCriterioMun
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
