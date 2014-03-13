<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pais
 *
 * @ORM\Table(name="pais")
 * @ORM\Entity
 */
class Pais
{
    /**
     * @var string
     *
     * @ORM\Column(name="sg_pais", type="string", length=5, nullable=true)
     */
    private $sgPais;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_pais", type="string", length=255, nullable=false)
     */
    private $nmPais;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_ordem", type="integer", nullable=true)
     */
    private $intOrdem;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_pais", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPais;



    /**
     * Set sgPais
     *
     * @param string $sgPais
     * @return Pais
     */
    public function setSgPais($sgPais)
    {
        $this->sgPais = $sgPais;

        return $this;
    }

    /**
     * Get sgPais
     *
     * @return string 
     */
    public function getSgPais()
    {
        return $this->sgPais;
    }

    /**
     * Set nmPais
     *
     * @param string $nmPais
     * @return Pais
     */
    public function setNmPais($nmPais)
    {
        $this->nmPais = $nmPais;

        return $this;
    }

    /**
     * Get nmPais
     *
     * @return string 
     */
    public function getNmPais()
    {
        return $this->nmPais;
    }

    /**
     * Set intOrdem
     *
     * @param integer $intOrdem
     * @return Pais
     */
    public function setIntOrdem($intOrdem)
    {
        $this->intOrdem = $intOrdem;

        return $this;
    }

    /**
     * Get intOrdem
     *
     * @return integer 
     */
    public function getIntOrdem()
    {
        return $this->intOrdem;
    }

    /**
     * Set fgAtivo
     *
     * @param integer $fgAtivo
     * @return Pais
     */
    public function setFgAtivo($fgAtivo)
    {
        $this->fgAtivo = $fgAtivo;

        return $this;
    }

    /**
     * Get fgAtivo
     *
     * @return integer 
     */
    public function getFgAtivo()
    {
        return $this->fgAtivo;
    }

    /**
     * Get idPais
     *
     * @return integer 
     */
    public function getIdPais()
    {
        return $this->idPais;
    }
}
