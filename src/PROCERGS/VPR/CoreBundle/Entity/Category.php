<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity
 */
class Category
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
     * @var integer
     *
     * @ORM\Column(name="sorting", type="integer")
     * @Groups({"vote", "setup"})
     */
    protected $sorting;

    /**
     * @ORM\OneToMany(targetEntity="PollOption", mappedBy="category")
     */
    protected $pollOptions;

    /**
     * @var string
     *
     * @ORM\Column(name="title_bg", type="string", length=7)
     * @Groups({"setup"})
     */
    protected $titleBg;

    /**
     * @var string
     *
     * @ORM\Column(name="icon_bg", type="string", length=7)
     * @Groups({"setup"})
     */
    protected $iconBg;

    /**
     * @var string
     *
     * @ORM\Column(name="option_bg", type="string", length=7)
     * @Groups({"setup"})
     */
    protected $optionBg;

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
     * @return Category
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
     * @return Category
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
     * Set title_bg
     *
     * @param string $title_bg
     * @return Category
     */
    public function setTitleBg($titleBg)
    {
        $this->titleBg = $titleBg;

        return $this;
    }

    /**
     * Get title_bg
     *
     * @return string
     */
    public function getTitleBg()
    {
        return $this->titleBg;
    }

    /**
     * Set icon_bg
     *
     * @param string $icon_bg
     * @return Category
     */
    public function setIconBg($iconBg)
    {
        $this->iconBg = $iconBg;

        return $this;
    }

    /**
     * Get icon_bg
     *
     * @return string
     */
    public function getIconBg()
    {
        return $this->iconBg;
    }

     /**
     * Set option_bg
     *
     * @param string $option_bg
     * @return Category
     */
    public function setOptionBg($optionBg)
    {
        $this->optionBg = $optionBg;

        return $this;
    }

    /**
     * Get option_bg
     *
     * @return string
     */
    public function getOptionBg()
    {
        return $this->optionBg;
    }

}
