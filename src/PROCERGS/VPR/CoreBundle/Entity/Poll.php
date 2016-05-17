<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Poll
 *
 * @ORM\Table(name="poll")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\PollRepository")
 */
class Poll
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"vote", "setup"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"vote", "setup"})
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="opening_time", type="datetime", nullable=false)
     * @Groups({"vote", "setup"})
     */
    protected $openingTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closing_time", type="datetime", nullable=false)
     * @Groups({"vote", "setup"})
     */
    protected $closingTime;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Groups({"vote", "setup"})
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="text")
     * @Groups("setup")
     */
    protected $publicKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="apuration_time", type="datetime", nullable=false)
     * @Groups({"vote", "setup"})
     */
    protected $apurationTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="apuration_done", type="datetime", nullable=false)
     * @Groups({"vote", "setup"})
     */
    protected $apurationDone;

    /**
     * @ORM\OneToMany(targetEntity="BallotBox", mappedBy="poll")
     */
    protected $ballotBoxes;

    /**
     * @ORM\OneToMany(targetEntity="Step", mappedBy="poll")
     * @ORM\OrderBy({"sorting" = "asc"})
     * @Groups("setup")
     */
    protected $steps;

    public function __construct()
    {
        //$this->ballotBoxes = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Poll
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set openingTime
     *
     * @param \DateTime $openingTime
     * @return Poll
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
     * Set closingTimime
     *
     * @param \DateTime $closingTimime
     * @return Poll
     */
    public function setClosingTime($var)
    {
        $this->closingTime = $var;

        return $this;
    }

    /**
     * Get closingTimime
     *
     * @return \DateTime
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Poll
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set publicKey
     *
     * @param string $publicKey
     * @return Poll
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get publicKey
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Set apurationTime
     *
     * @param \DateTime $apurationTime
     * @return Poll
     */
    public function setApurationTime($apurationTime)
    {
        $this->apurationTime = $apurationTime;

        return $this;
    }

    /**
     * Get apurationTime
     *
     * @return \DateTime
     */
    public function getApurationTime()
    {
        return $this->apurationTime;
    }

    /**
     * Set apurationDone
     *
     * @param \DateTime $apurationDone
     * @return Poll
     */
    public function setApurationDone($apurationDone)
    {
        $this->apurationDone = $apurationDone;

        return $this;
    }

    /**
     * Get apurationDone
     *
     * @return \DateTime
     */
    public function getApurationDone()
    {
        return $this->apurationDone;
    }

    public function getBallotBoxes()
    {
        return $this->ballotBoxes;
    }

    public function getSteps()
    {
        return $this->steps;
    }
}
