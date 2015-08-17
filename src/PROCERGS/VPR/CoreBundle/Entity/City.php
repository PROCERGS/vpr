<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\CityRepository")
 */
class City
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @Groups({"autocomplete", "setup"})
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="ibge_code", type="integer")
     * @Groups("setup")
     */
    protected $ibgeCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_sefa", type="integer")
     * @Groups("setup")
     */
    protected $codSefa;

    /**
     * @var string
     *
     * @Groups({"autocomplete", "setup"})
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_capital", type="boolean")
     * @Groups("setup")
     */
    protected $isCapital;

    /**
     * @ORM\ManyToOne(targetEntity="Corede", inversedBy="cities")
     * @ORM\JoinColumn(name="corede_id", referencedColumnName="id")
     * @Groups("setup")
     */
    protected $corede;

    /**
     * @ORM\OneToMany(targetEntity="BallotBox", mappedBy="city")
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

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\Corede
     */
    public function getCorede()
    {
        return $this->corede;
    }
}
