<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cedula
 *
 * @ORM\Table(name="cedula", indexes={@ORM\Index(name="fk_cedula_votacao", columns={"id_votacao"}), @ORM\Index(name="fk_cedula_regiao", columns={"id_regiao"}), @ORM\Index(name="fk_cedula_grupo_demanda", columns={"id_grupo_demanda"}), @ORM\Index(name="fk_cedula_area_tematica", columns={"id_area_tematica"})})
 * @ORM\Entity
 */
class Cedula
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao", type="integer", nullable=false)
     */
    private $idVotacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_regiao", type="integer", nullable=false)
     */
    private $idRegiao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_grupo_demanda", type="integer", nullable=false)
     */
    private $idGrupoDemanda;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_area_tematica", type="integer", nullable=true)
     */
    private $idAreaTematica;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_demanda", type="string", length=255, nullable=true)
     */
    private $nmDemanda;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_demanda", type="text", nullable=true)
     */
    private $dsDemanda;

    /**
     * @var string
     *
     * @ORM\Column(name="vlr_demanda", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $vlrDemanda;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_projeto", type="integer", nullable=true)
     */
    private $codProjeto;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_abrangencia", type="text", nullable=true)
     */
    private $dsAbrangencia;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_projeto_cedula", type="text", nullable=false)
     */
    private $nmProjetoCedula;

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
     * @ORM\Column(name="cod_cedula_pop", type="integer", nullable=true)
     */
    private $codCedulaPop;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cedula", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCedula;



    /**
     * Set idVotacao
     *
     * @param integer $idVotacao
     * @return Cedula
     */
    public function setIdVotacao($idVotacao)
    {
        $this->idVotacao = $idVotacao;

        return $this;
    }

    /**
     * Get idVotacao
     *
     * @return integer 
     */
    public function getIdVotacao()
    {
        return $this->idVotacao;
    }

    /**
     * Set idRegiao
     *
     * @param integer $idRegiao
     * @return Cedula
     */
    public function setIdRegiao($idRegiao)
    {
        $this->idRegiao = $idRegiao;

        return $this;
    }

    /**
     * Get idRegiao
     *
     * @return integer 
     */
    public function getIdRegiao()
    {
        return $this->idRegiao;
    }

    /**
     * Set idGrupoDemanda
     *
     * @param integer $idGrupoDemanda
     * @return Cedula
     */
    public function setIdGrupoDemanda($idGrupoDemanda)
    {
        $this->idGrupoDemanda = $idGrupoDemanda;

        return $this;
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

    /**
     * Set idAreaTematica
     *
     * @param integer $idAreaTematica
     * @return Cedula
     */
    public function setIdAreaTematica($idAreaTematica)
    {
        $this->idAreaTematica = $idAreaTematica;

        return $this;
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

    /**
     * Set nmDemanda
     *
     * @param string $nmDemanda
     * @return Cedula
     */
    public function setNmDemanda($nmDemanda)
    {
        $this->nmDemanda = $nmDemanda;

        return $this;
    }

    /**
     * Get nmDemanda
     *
     * @return string 
     */
    public function getNmDemanda()
    {
        return $this->nmDemanda;
    }

    /**
     * Set dsDemanda
     *
     * @param string $dsDemanda
     * @return Cedula
     */
    public function setDsDemanda($dsDemanda)
    {
        $this->dsDemanda = $dsDemanda;

        return $this;
    }

    /**
     * Get dsDemanda
     *
     * @return string 
     */
    public function getDsDemanda()
    {
        return $this->dsDemanda;
    }

    /**
     * Set vlrDemanda
     *
     * @param string $vlrDemanda
     * @return Cedula
     */
    public function setVlrDemanda($vlrDemanda)
    {
        $this->vlrDemanda = $vlrDemanda;

        return $this;
    }

    /**
     * Get vlrDemanda
     *
     * @return string 
     */
    public function getVlrDemanda()
    {
        return $this->vlrDemanda;
    }

    /**
     * Set codProjeto
     *
     * @param integer $codProjeto
     * @return Cedula
     */
    public function setCodProjeto($codProjeto)
    {
        $this->codProjeto = $codProjeto;

        return $this;
    }

    /**
     * Get codProjeto
     *
     * @return integer 
     */
    public function getCodProjeto()
    {
        return $this->codProjeto;
    }

    /**
     * Set dsAbrangencia
     *
     * @param string $dsAbrangencia
     * @return Cedula
     */
    public function setDsAbrangencia($dsAbrangencia)
    {
        $this->dsAbrangencia = $dsAbrangencia;

        return $this;
    }

    /**
     * Get dsAbrangencia
     *
     * @return string 
     */
    public function getDsAbrangencia()
    {
        return $this->dsAbrangencia;
    }

    /**
     * Set nmProjetoCedula
     *
     * @param string $nmProjetoCedula
     * @return Cedula
     */
    public function setNmProjetoCedula($nmProjetoCedula)
    {
        $this->nmProjetoCedula = $nmProjetoCedula;

        return $this;
    }

    /**
     * Get nmProjetoCedula
     *
     * @return string 
     */
    public function getNmProjetoCedula()
    {
        return $this->nmProjetoCedula;
    }

    /**
     * Set intOrdem
     *
     * @param integer $intOrdem
     * @return Cedula
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
     * @return Cedula
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
     * Set codCedulaPop
     *
     * @param integer $codCedulaPop
     * @return Cedula
     */
    public function setCodCedulaPop($codCedulaPop)
    {
        $this->codCedulaPop = $codCedulaPop;

        return $this;
    }

    /**
     * Get codCedulaPop
     *
     * @return integer 
     */
    public function getCodCedulaPop()
    {
        return $this->codCedulaPop;
    }

    /**
     * Get idCedula
     *
     * @return integer 
     */
    public function getIdCedula()
    {
        return $this->idCedula;
    }
}
