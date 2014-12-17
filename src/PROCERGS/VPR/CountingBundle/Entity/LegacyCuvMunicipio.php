<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuvMunicipio
 *
 * @ORM\Table(name="legacy_cuv_municipio")
 * @ORM\Entity
 */
class LegacyCuvMunicipio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="cuv_id_uf", type="integer", nullable=true)
     */
    private $cuvIdUf;

    /**
     * @var integer
     *
     * @ORM\Column(name="cuv_id_pais", type="integer", nullable=false)
     */
    private $cuvIdPais;



    /**
     * Set name
     *
     * @param string $name
     * @return CuvMunicipio
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
     * Set cuvIdUf
     *
     * @param integer $cuvIdUf
     * @return CuvMunicipio
     */
    public function setCuvIdUf($cuvIdUf)
    {
        $this->cuvIdUf = $cuvIdUf;

        return $this;
    }

    /**
     * Get cuvIdUf
     *
     * @return integer 
     */
    public function getCuvIdUf()
    {
        return $this->cuvIdUf;
    }

    /**
     * Set cuvIdPais
     *
     * @param integer $cuvIdPais
     * @return CuvMunicipio
     */
    public function setCuvIdPais($cuvIdPais)
    {
        $this->cuvIdPais = $cuvIdPais;

        return $this;
    }

    /**
     * Get cuvIdPais
     *
     * @return integer 
     */
    public function getCuvIdPais()
    {
        return $this->cuvIdPais;
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
}
