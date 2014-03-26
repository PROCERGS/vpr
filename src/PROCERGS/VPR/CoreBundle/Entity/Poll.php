<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Poll
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Poll
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"vote"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"vote"})
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="openingTime", type="datetime")
     * @Groups({"vote"})
     */
    private $openingTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closingTimime", type="datetime")
     * @Groups({"vote"})
     */
    private $closingTimime;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Groups({"vote"})
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="publicKey", type="text")
     */
    private $publicKey;

    /**
     * @ORM\OneToMany(targetEntity="BallotBox", mappedBy="poll")
     */
    protected $ballotBoxes;

    /**
     * @ORM\OneToMany(targetEntity="Step", mappedBy="poll")
     */
    protected $steps;

    public function __construct()
    {
        $this->ballotBoxes = new ArrayCollection();
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
    public function setClosingTimime($closingTimime)
    {
        $this->closingTimime = $closingTimime;

        return $this;
    }

    /**
     * Get closingTimime
     *
     * @return \DateTime
     */
    public function getClosingTimime()
    {
        return $this->closingTimime;
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

    public function getBallotBoxes()
    {
        return $this->ballotBoxes;
    }

}
