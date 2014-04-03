<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * PollOption
 *
 * @ORM\Table(name="poll_option")
 * @ORM\Entity
 */
class PollOption
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"vote"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Groups({"vote"})
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="coverage", type="text", nullable=true)
     * @Groups({"vote"})
     */
    private $scope;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", type="decimal")
     * @Groups({"vote"})
     */
    private $cost;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_sorting", type="integer")
     * @Groups({"vote"})
     */
    private $categorySorting;

    /**
     * @ORM\ManyToOne(targetEntity="Poll")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id")
     * @Groups({"vote"})
     */
    protected $poll;

    /**
     * @ORM\ManyToOne(targetEntity="Step", inversedBy="pollOptions")
     * @ORM\JoinColumn(name="step_id", referencedColumnName="id")
     * @Groups({"vote"})
     */
    protected $step;

    /**
     * @ORM\ManyToOne(targetEntity="Corede")
     * @ORM\JoinColumn(name="corede_id", referencedColumnName="id")
     * @Groups({"vote"})
     */
    protected $corede;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="pollOptions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Groups({"vote"})
     */
    protected $category;

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
     * Set title
     *
     * @param string $title
     * @return PollOption
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return PollOption
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
     * Set scope
     *
     * @param string $scope
     * @return PollOption
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set cost
     *
     * @param string $cost
     * @return PollOption
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set categorySorting
     *
     * @param integer $categorySorting
     * @return PollOption
     */
    public function setCategorySorting($categorySorting)
    {
        $this->categorySorting = $categorySorting;

        return $this;
    }

    /**
     * Get categorySorting
     *
     * @return integer
     */
    public function getCategorySorting()
    {
        return $this->categorySorting;
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\Step
     */
    public function getStep()
    {
        return $this->step;
    }

}
