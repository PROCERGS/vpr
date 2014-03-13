<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EleitorTre2012
 *
 * @ORM\Table(name="eleitor_tre_2012", uniqueConstraints={@ORM\UniqueConstraint(name="nro_titulo_UNIQUE", columns={"nro_titulo"})}, indexes={@ORM\Index(name="fk_eleitor_tre_municipio", columns={"cod_mun_tre"})})
 * @ORM\Entity
 */
class EleitorTre2012
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cod_mun_tre", type="integer", nullable=false)
     */
    private $codMunTre;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_mun_tre", type="string", length=255, nullable=true)
     */
    private $nmMunTre;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_zona", type="integer", nullable=true)
     */
    private $intZona;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_titulo", type="bigint", nullable=false)
     */
    private $nroTitulo;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_eleitor", type="string", length=255, nullable=false)
     */
    private $nmEleitor;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_eleitor_tre", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEleitorTre;



    /**
     * Set codMunTre
     *
     * @param integer $codMunTre
     * @return EleitorTre2012
     */
    public function setCodMunTre($codMunTre)
    {
        $this->codMunTre = $codMunTre;

        return $this;
    }

    /**
     * Get codMunTre
     *
     * @return integer 
     */
    public function getCodMunTre()
    {
        return $this->codMunTre;
    }

    /**
     * Set nmMunTre
     *
     * @param string $nmMunTre
     * @return EleitorTre2012
     */
    public function setNmMunTre($nmMunTre)
    {
        $this->nmMunTre = $nmMunTre;

        return $this;
    }

    /**
     * Get nmMunTre
     *
     * @return string 
     */
    public function getNmMunTre()
    {
        return $this->nmMunTre;
    }

    /**
     * Set intZona
     *
     * @param integer $intZona
     * @return EleitorTre2012
     */
    public function setIntZona($intZona)
    {
        $this->intZona = $intZona;

        return $this;
    }

    /**
     * Get intZona
     *
     * @return integer 
     */
    public function getIntZona()
    {
        return $this->intZona;
    }

    /**
     * Set nroTitulo
     *
     * @param integer $nroTitulo
     * @return EleitorTre2012
     */
    public function setNroTitulo($nroTitulo)
    {
        $this->nroTitulo = $nroTitulo;

        return $this;
    }

    /**
     * Get nroTitulo
     *
     * @return integer 
     */
    public function getNroTitulo()
    {
        return $this->nroTitulo;
    }

    /**
     * Set nmEleitor
     *
     * @param string $nmEleitor
     * @return EleitorTre2012
     */
    public function setNmEleitor($nmEleitor)
    {
        $this->nmEleitor = $nmEleitor;

        return $this;
    }

    /**
     * Get nmEleitor
     *
     * @return string 
     */
    public function getNmEleitor()
    {
        return $this->nmEleitor;
    }

    /**
     * Get idEleitorTre
     *
     * @return integer 
     */
    public function getIdEleitorTre()
    {
        return $this->idEleitorTre;
    }
}
