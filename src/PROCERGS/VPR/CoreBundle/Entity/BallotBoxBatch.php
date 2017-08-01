<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * RlAgency
 *
 * @ORM\Table(name="ballot_box_batch")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\BallotBoxBatchRepository")
 */
class BallotBoxBatch
{
    const STATUS_AGUARDA_PROCESSAMENTO = 0;
    const STATUS_PROCESSANDO = 2;
    const STATUS_PROCESSADO = 3;

    /**
     * @var string
     *
     * @ORM\Column(name="csv_input_name", type="string", length=60, nullable=false)
     */
    private $csvInputName;
    /**
     * @var string
     *
     * @ORM\Column(name="csv_input", type="text", nullable=false)
     */
    private $csvInput;
    /**
     * @var string
     *
     * @ORM\Column(name="csv_output", type="text", nullable=false)
     */
    private $csvOutput;
    /**
     * @var integer
     *
     * @ORM\Column(name="status1", type="integer")
     */
    private $status1;
    /**
     * @var integer
     *
     * @ORM\Column(name="tot_ok", type="integer")
     */
    private $totOk;
    /**
     * @var integer
     *
     * @ORM\Column(name="tot_fail", type="integer")
     */
    private $totFail;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="opening_time", type="datetime", nullable=true)
     * @Groups({"vote", "setup"})
     */
    private $openingTime;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closing_time", type="datetime", nullable=true)
     * @Groups({"vote", "setup"})
     */
    private $closingTime;    

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="ballot_box_batch_id_seq", allocationSize=1, initialValue=1)
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
     * @return BallotBoxBatch
     */
    public function setCsvInputName($val)
    {
        $this->csvInputName = $val;

        return $this;
    }

    /**
     * @return string 
     */
    public function getCsvInputName()
    {
        return $this->csvInputName;
    }
    
    /**
     * @return BallotBoxBatch
     */
    public function setCsvInput($val)
    {
        $this->csvInput = $val;
    
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCsvInput()
    {
        return $this->csvInput;
    }
    /**
     * @return BallotBoxBatch
     */
    public function setCsvOutput($val)
    {
        $this->csvOutput = $val;
    
        return $this;
    }
    /**
     * @return string
     */
    public function getCsvOutput()
    {
        return $this->csvOutput;
    }
    /**
     * @return BallotBoxBatch
     */
    public function setStatus1($val)
    {
        $this->status1 = $val;
    
        return $this;
    }
    /**
     * @return string
     */
    public function getStatus1()
    {
        return $this->status1;
    }
    /**
     * @return BallotBoxBatch
     */
    public function setTotOk($val)
    {
        $this->totOk = $val;
    
        return $this;
    }
    /**
     * @return string
     */
    public function getTotOk()
    {
        return $this->totOk;
    }
    /**
     * @return BallotBoxBatch
     */
    public function setTotFail($val)
    {
        $this->totFail = $val;
    
        return $this;
    }
    /**
     * @return string
     */
    public function getTotFail()
    {
        return $this->totFail;
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
     * @return BallotBoxBatch
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
     * Set openingTime
     *
     * @param \DateTime $openingTime
     * @return BallotBoxBatch
     */
    public function setOpeningTime($openingTime)
    {
        $this->openingTime = $openingTime;
    
        return $this;
    }
    
    /**
     * Get openingTime
     *
     * @return \DateTime
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }
    
    /**
     * Set closingTime
     *
     * @param \DateTime $closingTime
     * @return BallotBoxBatch
     */
    public function setClosingTime($closingTime)
    {
        $this->closingTime = $closingTime;
    
        return $this;
    }
    
    /**
     * Get closingTime
     *
     * @return \DateTime
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }
}
