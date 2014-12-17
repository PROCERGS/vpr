<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AreaTematica
 *
 * @ORM\Table(name="legacy_area_tematica")
 * @ORM\Entity
 */
class LegacyAreaTematica
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_area_tematica", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAreaTematica;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_area_tematica", type="string", length=255, nullable=false)
     */
    private $nmAreaTematica;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_area_abrev", type="string", length=20, nullable=true)
     */
    private $nmAreaAbrev;

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
     * Set nmAreaTematica
     *
     * @param string $nmAreaTematica
     * @return AreaTematica
     */
    public function setNmAreaTematica($nmAreaTematica)
    {
        $this->nmAreaTematica = $nmAreaTematica;

        return $this;
    }

    /**
     * Get nmAreaTematica
     *
     * @return string 
     */
    public function getNmAreaTematica()
    {
        return $this->nmAreaTematica;
    }

    /**
     * Set nmAreaAbrev
     *
     * @param string $nmAreaAbrev
     * @return AreaTematica
     */
    public function setNmAreaAbrev($nmAreaAbrev)
    {
        $this->nmAreaAbrev = $nmAreaAbrev;

        return $this;
    }

    /**
     * Get nmAreaAbrev
     *
     * @return string 
     */
    public function getNmAreaAbrev()
    {
        return $this->nmAreaAbrev;
    }

    /**
     * Set intOrdem
     *
     * @param integer $intOrdem
     * @return AreaTematica
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
     * @return AreaTematica
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
     * Get idAreaTematica
     *
     * @return integer 
     */
    public function getIdAreaTematica()
    {
        return $this->idAreaTematica;
    }
}
