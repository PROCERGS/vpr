<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Step
 *
 * @ORM\Table(name="step")
 * @ORM\Entity
 */
class Step
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
     * @var integer
     *
     * @ORM\Column(name="sorting", type="integer")
     * @Groups({"vote"})
     */
    private $sorting;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_selection", type="integer")
     * @Groups({"vote"})
     */
    private $maxSelection;

    /**
     * @ORM\OneToMany(targetEntity="PollOption", mappedBy="step")
     */
    protected $pollOptions;

    /**
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="steps")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id")
     * @Groups({"vote"})
     */
    protected $poll;

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
     * @return Step
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
     * Set sorting
     *
     * @param integer $sorting
     * @return Step
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Get sorting
     *
     * @return integer
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Set maxSelection
     *
     * @param integer $maxSelection
     * @return Step
     */
    public function setMaxSelection($maxSelection)
    {
        $this->maxSelection = $maxSelection;

        return $this;
    }

    /**
     * Get maxSelection
     *
     * @return integer
     */
    public function getMaxSelection()
    {
        return $this->maxSelection;
    }

    /**
     * @param \PROCERGS\VPR\CoreBundle\Entity\Poll $poll
     * @return \PROCERGS\VPR\CoreBundle\Entity\Poll
     */
    public function setPoll(Poll $poll)
    {
        $this->poll = $poll;

        return $this;
    }

    /**
     * @return Poll
     */
    public function getPoll()
    {
        return $this->poll;
    }


    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\PollOption
     */
    public function getPollOptions()
    {
        return $this->pollOptions;
    }
}
