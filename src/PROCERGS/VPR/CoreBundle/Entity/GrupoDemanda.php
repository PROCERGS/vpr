<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GrupoDemanda
 *
 * @ORM\Table(name="grupo_demanda")
 * @ORM\Entity
 */
class GrupoDemanda
{
    /**
     * @var string
     *
     * @ORM\Column(name="nm_grupo_demanda", type="string", length=255, nullable=false)
     */
    private $nmGrupoDemanda;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_grupo_abrev", type="string", length=40, nullable=true)
     */
    private $nmGrupoAbrev;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_grupo_demanda", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGrupoDemanda;



    /**
     * Set nmGrupoDemanda
     *
     * @param string $nmGrupoDemanda
     * @return GrupoDemanda
     */
    public function setNmGrupoDemanda($nmGrupoDemanda)
    {
        $this->nmGrupoDemanda = $nmGrupoDemanda;

        return $this;
    }

    /**
     * Get nmGrupoDemanda
     *
     * @return string 
     */
    public function getNmGrupoDemanda()
    {
        return $this->nmGrupoDemanda;
    }

    /**
     * Set nmGrupoAbrev
     *
     * @param string $nmGrupoAbrev
     * @return GrupoDemanda
     */
    public function setNmGrupoAbrev($nmGrupoAbrev)
    {
        $this->nmGrupoAbrev = $nmGrupoAbrev;

        return $this;
    }

    /**
     * Get nmGrupoAbrev
     *
     * @return string 
     */
    public function getNmGrupoAbrev()
    {
        return $this->nmGrupoAbrev;
    }

    /**
     * Set fgAtivo
     *
     * @param integer $fgAtivo
     * @return GrupoDemanda
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
     * Get idGrupoDemanda
     *
     * @return integer 
     */
    public function getIdGrupoDemanda()
    {
        return $this->idGrupoDemanda;
    }
}
