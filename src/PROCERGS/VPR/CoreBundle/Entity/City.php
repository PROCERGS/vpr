<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class City
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="treCode", type="integer")
     */
    private $treCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="ibgeCode", type="integer")
     */
    private $ibgeCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="codSefa", type="integer")
     */
    private $codSefa;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isCapital", type="boolean")
     */
    private $isCapital;

    /**
     * @ORM\ManyToOne(targetEntity="Corede", inversedBy="cities")
     * @ORM\JoinColumn(name="corede_id", referencedColumnName="id")
     */
    protected $corede;

    /**
     * @ORM\OneToMany(targetEntity="BallotBox", mappedBy="poll")
     */
    protected $ballotBoxes;

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
     * Set treCode
     *
     * @param integer $treCode
     * @return City
     */
    public function setTreCode($treCode)
    {
        $this->treCode = $treCode;

        return $this;
    }

    /**
     * Get treCode
     *
     * @return integer
     */
    public function getTreCode()
    {
        return $this->treCode;
    }

    /**
     * Set ibgeCode
     *
     * @param integer $ibgeCode
     * @return City
     */
    public function setIbgeCode($ibgeCode)
    {
        $this->ibgeCode = $ibgeCode;

        return $this;
    }

    /**
     * Get ibgeCode
     *
     * @return integer
     */
    public function getIbgeCode()
    {
        return $this->ibgeCode;
    }

    /**
     * Set codSefa
     *
     * @param integer $codSefa
     * @return City
     */
    public function setCodSefa($codSefa)
    {
        $this->codSefa = $codSefa;

        return $this;
    }

    /**
     * Get codSefa
     *
     * @return integer
     */
    public function getCodSefa()
    {
        return $this->codSefa;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return City
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
     * Set isCapital
     *
     * @param boolean $isCapital
     * @return City
     */
    public function setIsCapital($isCapital)
    {
        $this->isCapital = $isCapital;

        return $this;
    }

    /**
     * Get isCapital
     *
     * @return boolean
     */
    public function getIsCapital()
    {
        return $this->isCapital;
    }

}
