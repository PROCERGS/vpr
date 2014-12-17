<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UrnaOld
 *
 * @ORM\Table(name="legacy_urna_old", indexes={@ORM\Index(name="urna_old_FKIndex1", columns={"id_votacao"}), @ORM\Index(name="urna_old_FKIndex2", columns={"id_regiao"}), @ORM\Index(name="urna_old_FKIndex3", columns={"id_municipio"})})
 * @ORM\Entity
 */
class LegacyUrnaOld
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_urna", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUrna;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao", type="integer", nullable=false)
     */
    private $idVotacao = '2';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_regiao", type="integer", nullable=false)
     */
    private $idRegiao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_municipio", type="integer", nullable=true)
     */
    private $idMunicipio;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_urna", type="integer", nullable=false)
     */
    private $nroUrna;

    /**
     * @var string
     *
     * @ORM\Column(name="txt_localizacao", type="string", length=255, nullable=true)
     */
    private $txtLocalizacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_votantes", type="integer", nullable=true)
     */
    private $nroVotantes;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_votos_brancos", type="integer", nullable=true)
     */
    private $nroVotosBrancos;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_votos_nulos", type="integer", nullable=true)
     */
    private $nroVotosNulos;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_ano_corrente", type="integer", nullable=true)
     */
    private $nroAnoCorrente;

    /**
     * @var integer
     *
     * @ORM\Column(name="ind_apurada", type="integer", nullable=true)
     */
    private $indApurada;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_mun_pop", type="integer", nullable=true)
     */
    private $codMunPop;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ctr_dth_inc", type="datetime", nullable=true)
     */
    private $ctrDthInc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ctr_dth_atu", type="datetime", nullable=true)
     */
    private $ctrDthAtu;

    /**
     * @var integer
     *
     * @ORM\Column(name="ctr_usu_inc", type="integer", nullable=true)
     */
    private $ctrUsuInc;

    /**
     * @var integer
     *
     * @ORM\Column(name="ctr_usu_atu", type="integer", nullable=true)
     */
    private $ctrUsuAtu;

    /**
     * @var string
     *
     * @ORM\Column(name="ctr_nro_ip_inc", type="string", length=20, nullable=true)
     */
    private $ctrNroIpInc;

    /**
     * @var string
     *
     * @ORM\Column(name="ctr_nro_ip_atu", type="string", length=20, nullable=true)
     */
    private $ctrNroIpAtu;



    /**
     * Set idVotacao
     *
     * @param integer $idVotacao
     * @return UrnaOld
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
     * @return UrnaOld
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
     * Set idMunicipio
     *
     * @param integer $idMunicipio
     * @return UrnaOld
     */
    public function setIdMunicipio($idMunicipio)
    {
        $this->idMunicipio = $idMunicipio;

        return $this;
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
     * Set nroUrna
     *
     * @param integer $nroUrna
     * @return UrnaOld
     */
    public function setNroUrna($nroUrna)
    {
        $this->nroUrna = $nroUrna;

        return $this;
    }

    /**
     * Get nroUrna
     *
     * @return integer 
     */
    public function getNroUrna()
    {
        return $this->nroUrna;
    }

    /**
     * Set txtLocalizacao
     *
     * @param string $txtLocalizacao
     * @return UrnaOld
     */
    public function setTxtLocalizacao($txtLocalizacao)
    {
        $this->txtLocalizacao = $txtLocalizacao;

        return $this;
    }

    /**
     * Get txtLocalizacao
     *
     * @return string 
     */
    public function getTxtLocalizacao()
    {
        return $this->txtLocalizacao;
    }

    /**
     * Set nroVotantes
     *
     * @param integer $nroVotantes
     * @return UrnaOld
     */
    public function setNroVotantes($nroVotantes)
    {
        $this->nroVotantes = $nroVotantes;

        return $this;
    }

    /**
     * Get nroVotantes
     *
     * @return integer 
     */
    public function getNroVotantes()
    {
        return $this->nroVotantes;
    }

    /**
     * Set nroVotosBrancos
     *
     * @param integer $nroVotosBrancos
     * @return UrnaOld
     */
    public function setNroVotosBrancos($nroVotosBrancos)
    {
        $this->nroVotosBrancos = $nroVotosBrancos;

        return $this;
    }

    /**
     * Get nroVotosBrancos
     *
     * @return integer 
     */
    public function getNroVotosBrancos()
    {
        return $this->nroVotosBrancos;
    }

    /**
     * Set nroVotosNulos
     *
     * @param integer $nroVotosNulos
     * @return UrnaOld
     */
    public function setNroVotosNulos($nroVotosNulos)
    {
        $this->nroVotosNulos = $nroVotosNulos;

        return $this;
    }

    /**
     * Get nroVotosNulos
     *
     * @return integer 
     */
    public function getNroVotosNulos()
    {
        return $this->nroVotosNulos;
    }

    /**
     * Set nroAnoCorrente
     *
     * @param integer $nroAnoCorrente
     * @return UrnaOld
     */
    public function setNroAnoCorrente($nroAnoCorrente)
    {
        $this->nroAnoCorrente = $nroAnoCorrente;

        return $this;
    }

    /**
     * Get nroAnoCorrente
     *
     * @return integer 
     */
    public function getNroAnoCorrente()
    {
        return $this->nroAnoCorrente;
    }

    /**
     * Set indApurada
     *
     * @param integer $indApurada
     * @return UrnaOld
     */
    public function setIndApurada($indApurada)
    {
        $this->indApurada = $indApurada;

        return $this;
    }

    /**
     * Get indApurada
     *
     * @return integer 
     */
    public function getIndApurada()
    {
        return $this->indApurada;
    }

    /**
     * Set codMunPop
     *
     * @param integer $codMunPop
     * @return UrnaOld
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
     * Set ctrDthInc
     *
     * @param \DateTime $ctrDthInc
     * @return UrnaOld
     */
    public function setCtrDthInc($ctrDthInc)
    {
        $this->ctrDthInc = $ctrDthInc;

        return $this;
    }

    /**
     * Get ctrDthInc
     *
     * @return \DateTime 
     */
    public function getCtrDthInc()
    {
        return $this->ctrDthInc;
    }

    /**
     * Set ctrDthAtu
     *
     * @param \DateTime $ctrDthAtu
     * @return UrnaOld
     */
    public function setCtrDthAtu($ctrDthAtu)
    {
        $this->ctrDthAtu = $ctrDthAtu;

        return $this;
    }

    /**
     * Get ctrDthAtu
     *
     * @return \DateTime 
     */
    public function getCtrDthAtu()
    {
        return $this->ctrDthAtu;
    }

    /**
     * Set ctrUsuInc
     *
     * @param integer $ctrUsuInc
     * @return UrnaOld
     */
    public function setCtrUsuInc($ctrUsuInc)
    {
        $this->ctrUsuInc = $ctrUsuInc;

        return $this;
    }

    /**
     * Get ctrUsuInc
     *
     * @return integer 
     */
    public function getCtrUsuInc()
    {
        return $this->ctrUsuInc;
    }

    /**
     * Set ctrUsuAtu
     *
     * @param integer $ctrUsuAtu
     * @return UrnaOld
     */
    public function setCtrUsuAtu($ctrUsuAtu)
    {
        $this->ctrUsuAtu = $ctrUsuAtu;

        return $this;
    }

    /**
     * Get ctrUsuAtu
     *
     * @return integer 
     */
    public function getCtrUsuAtu()
    {
        return $this->ctrUsuAtu;
    }

    /**
     * Set ctrNroIpInc
     *
     * @param string $ctrNroIpInc
     * @return UrnaOld
     */
    public function setCtrNroIpInc($ctrNroIpInc)
    {
        $this->ctrNroIpInc = $ctrNroIpInc;

        return $this;
    }

    /**
     * Get ctrNroIpInc
     *
     * @return string 
     */
    public function getCtrNroIpInc()
    {
        return $this->ctrNroIpInc;
    }

    /**
     * Set ctrNroIpAtu
     *
     * @param string $ctrNroIpAtu
     * @return UrnaOld
     */
    public function setCtrNroIpAtu($ctrNroIpAtu)
    {
        $this->ctrNroIpAtu = $ctrNroIpAtu;

        return $this;
    }

    /**
     * Get ctrNroIpAtu
     *
     * @return string 
     */
    public function getCtrNroIpAtu()
    {
        return $this->ctrNroIpAtu;
    }

    /**
     * Get idUrna
     *
     * @return integer 
     */
    public function getIdUrna()
    {
        return $this->idUrna;
    }
}
