<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Municipio
 *
 * @ORM\Table(name="municipio", indexes={@ORM\Index(name="municipio_Index3", columns={"cod_mun_tre"}), @ORM\Index(name="fk_municipio_uf", columns={"id_uf"}), @ORM\Index(name="fk_municipio_regiao", columns={"id_regiao"})})
 * @ORM\Entity
 */
class Municipio
{
    /**
     * @var string
     *
     * @ORM\Column(name="nm_municipio", type="string", length=255, nullable=false)
     */
    private $nmMunicipio;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_mun_tre", type="integer", nullable=true)
     */
    private $codMunTre;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_mun_ibge", type="integer", nullable=true)
     */
    private $codMunIbge;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_mun_sefa", type="integer", nullable=true)
     */
    private $codMunSefa;

    /**
     * @var integer
     *
     * @ORM\Column(name="qtd_populacao", type="integer", nullable=true)
     */
    private $qtdPopulacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="qtd_eleitores", type="integer", nullable=true)
     */
    private $qtdEleitores;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_mun_pop", type="integer", nullable=true)
     */
    private $codMunPop;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_capital", type="integer", nullable=true)
     */
    private $fgCapital;

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
     * @ORM\Column(name="id_municipio", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMunicipio;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Uf
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Uf")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_uf", referencedColumnName="id_uf")
     * })
     */
    private $idUf;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Regiao
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Regiao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_regiao", referencedColumnName="id_regiao")
     * })
     */
    private $idRegiao;



    /**
     * Set nmMunicipio
     *
     * @param string $nmMunicipio
     * @return Municipio
     */
    public function setNmMunicipio($nmMunicipio)
    {
        $this->nmMunicipio = $nmMunicipio;

        return $this;
    }

    /**
     * Get nmMunicipio
     *
     * @return string 
     */
    public function getNmMunicipio()
    {
        return $this->nmMunicipio;
    }

    /**
     * Set codMunTre
     *
     * @param integer $codMunTre
     * @return Municipio
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
     * Set codMunIbge
     *
     * @param integer $codMunIbge
     * @return Municipio
     */
    public function setCodMunIbge($codMunIbge)
    {
        $this->codMunIbge = $codMunIbge;

        return $this;
    }

    /**
     * Get codMunIbge
     *
     * @return integer 
     */
    public function getCodMunIbge()
    {
        return $this->codMunIbge;
    }

    /**
     * Set codMunSefa
     *
     * @param integer $codMunSefa
     * @return Municipio
     */
    public function setCodMunSefa($codMunSefa)
    {
        $this->codMunSefa = $codMunSefa;

        return $this;
    }

    /**
     * Get codMunSefa
     *
     * @return integer 
     */
    public function getCodMunSefa()
    {
        return $this->codMunSefa;
    }

    /**
     * Set qtdPopulacao
     *
     * @param integer $qtdPopulacao
     * @return Municipio
     */
    public function setQtdPopulacao($qtdPopulacao)
    {
        $this->qtdPopulacao = $qtdPopulacao;

        return $this;
    }

    /**
     * Get qtdPopulacao
     *
     * @return integer 
     */
    public function getQtdPopulacao()
    {
        return $this->qtdPopulacao;
    }

    /**
     * Set qtdEleitores
     *
     * @param integer $qtdEleitores
     * @return Municipio
     */
    public function setQtdEleitores($qtdEleitores)
    {
        $this->qtdEleitores = $qtdEleitores;

        return $this;
    }

    /**
     * Get qtdEleitores
     *
     * @return integer 
     */
    public function getQtdEleitores()
    {
        return $this->qtdEleitores;
    }

    /**
     * Set codMunPop
     *
     * @param integer $codMunPop
     * @return Municipio
     */
    public function setCodMunPop($codMunPop)
    {
        $this->codMunPop = $codMunPop;

        return $this;
    }

    /**
     * Get codMunPop
     *
     * @return integer 
     */
    public function getCodMunPop()
    {
        return $this->codMunPop;
    }

    /**
     * Set fgCapital
     *
     * @param integer $fgCapital
     * @return Municipio
     */
    public function setFgCapital($fgCapital)
    {
        $this->fgCapital = $fgCapital;

        return $this;
    }

    /**
     * Get fgCapital
     *
     * @return integer 
     */
    public function getFgCapital()
    {
        return $this->fgCapital;
    }

    /**
     * Set intOrdem
     *
     * @param integer $intOrdem
     * @return Municipio
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
     * @return Municipio
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
     * Get idMunicipio
     *
     * @return integer 
     */
    public function getIdMunicipio()
    {
        return $this->idMunicipio;
    }

    /**
     * Set idUf
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Uf $idUf
     * @return Municipio
     */
    public function setIdUf(\PROCERGS\VPR\CoreBundle\Entity\Uf $idUf = null)
    {
        $this->idUf = $idUf;

        return $this;
    }

    /**
     * Get idUf
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Uf 
     */
    public function getIdUf()
    {
        return $this->idUf;
    }

    /**
     * Set idRegiao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Regiao $idRegiao
     * @return Municipio
     */
    public function setIdRegiao(\PROCERGS\VPR\CoreBundle\Entity\Regiao $idRegiao = null)
    {
        $this->idRegiao = $idRegiao;

        return $this;
    }

    /**
     * Get idRegiao
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Regiao 
     */
    public function getIdRegiao()
    {
        return $this->idRegiao;
    }
}
