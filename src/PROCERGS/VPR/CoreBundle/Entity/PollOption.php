<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * PollOption
 *
 * @ORM\Table(name="poll_option")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository")
 */
class PollOption
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"vote", "setup"})
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Groups({"vote", "setup"})
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="coverage", type="text", nullable=true)
     * @Groups({"vote", "setup"})
     */
    protected $scope;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", type="decimal", precision=12, scale=2, nullable=true)
     * @Groups({"vote", "setup"})
     */
    protected $cost;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_sorting", type="integer")
     * @Groups({"vote", "setup"})
     */
    protected $categorySorting;

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
     * @Groups({"vote", "setup"})
     */
    protected $category;

    /**
     * just to have compatibiliti to migrate to system ppp, this is not used in the system
     * @var string
     *
     * @ORM\Column(name="cod_desc_cedula", type="integer", nullable=true)
     */
    protected $codDescCedula;

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

    public function setStep($var)
    {
        $this->step = $var;
        return $this;
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\Step
     */
    public function getStep()
    {
        return $this->step;
    }

    public function setCategory($var)
    {
        $this->category = $var;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCorede($var)
    {
        $this->corede = $var;
        return $this;
    }

    public function getCorede()
    {
        return $this->corede;
    }

    public function setCodDescCedula($var)
    {
        $this->codDescCedula = $var;
        return $this;
    }

    public function getCodDescCedula()
    {
        return $this->codDescCedula;
    }

}
