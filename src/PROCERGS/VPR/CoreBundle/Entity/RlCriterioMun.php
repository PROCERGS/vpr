<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RlCriterioMun
 *
 * @ORM\Table(name="rl_criterio_mun")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\RlCriterioMunRepository") 
 * @ORM\Entity
 */
class RlCriterioMun
{
    /**
     * @var string
     *
     * @ORM\Column(name="perc_citizen", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $percCitizen;

    /**
     * @var string
     *
     * @ORM\Column(name="perc_voter", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $percVoter;

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
     * Set percCitizen
     *
     * @param string $percCitizen
     * @return RlCriterioMun
     */
    public function setPercCitizen($percCitizen)
    {
        $this->percCitizen = $percCitizen;

        return $this;
    }

    /**
     * Get percCitizen
     *
     * @return string 
     */
    public function getPercCitizen()
    {
        return $this->percCitizen;
    }

    /**
     * Set percVoter
     *
     * @param string $percVoter
     * @return RlCriterioMun
     */
    public function setPercVoter($percVoter)
    {
        $this->percVoter = $percVoter;

        return $this;
    }

    /**
     * Get percVoter
     *
     * @return string 
     */
    public function getPercVoter()
    {
        return $this->percVoter;
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
