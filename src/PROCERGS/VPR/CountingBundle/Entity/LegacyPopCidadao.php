<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PopCidadao
 *
 * @ORM\Table(name="legacy_pop_cidadao", indexes={@ORM\Index(name="pop_cidadao_nro_titulo", columns={"nro_titulo"}), @ORM\Index(name="pop_cidadao_nm_eleitor", columns={"nm_eleitor"})})
 * @ORM\Entity
 */
class LegacyPopCidadao
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
     * @var integer
     *
     * @ORM\Column(name="nro_titulo", type="bigint", nullable=false)
     */
    private $nroTitulo;

    /**
     * @var integer
     *
     * @ORM\Column(name="rg", type="bigint", nullable=true)
     */
    private $rg;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_mun_pop", type="integer", nullable=true)
     */
    private $codMunPop;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_eleitor", type="string", length=255, nullable=true)
     */
    private $nmEleitor;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=100, nullable=true)
     */
    private $dsEmail;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ctr_dth_inc", type="datetime", nullable=true)
     */
    private $ctrDthInc;

    /**
     * @var string
     *
     * @ORM\Column(name="ctr_nro_ip_inc", type="string", length=20, nullable=true)
     */
    private $ctrNroIpInc;

    /**
     * @var string
     *
     * @ORM\Column(name="uf", type="string", length=2, nullable=false)
     */
    private $uf = 'RS';

    /**
     * @var integer
     *
     * @ORM\Column(name="ano", type="integer", nullable=false)
     */
    private $ano = '2013';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';



    /**
     * Set nroTitulo
     *
     * @param integer $nroTitulo
     * @return PopCidadao
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
     * Set rg
     *
     * @param integer $rg
     * @return PopCidadao
     */
    public function setRg($rg)
    {
        $this->rg = $rg;

        return $this;
    }

    /**
     * Get rg
     *
     * @return integer 
     */
    public function getRg()
    {
        return $this->rg;
    }

    /**
     * Set codMunPop
     *
     * @param integer $codMunPop
     * @return PopCidadao
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
     * Set nmEleitor
     *
     * @param string $nmEleitor
     * @return PopCidadao
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
     * Set dsEmail
     *
     * @param string $dsEmail
     * @return PopCidadao
     */
    public function setDsEmail($dsEmail)
    {
        $this->dsEmail = $dsEmail;

        return $this;
    }

    /**
     * Get dsEmail
     *
     * @return string 
     */
    public function getDsEmail()
    {
        return $this->dsEmail;
    }

    /**
     * Set ctrDthInc
     *
     * @param \DateTime $ctrDthInc
     * @return PopCidadao
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
     * Set ctrNroIpInc
     *
     * @param string $ctrNroIpInc
     * @return PopCidadao
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
     * Set uf
     *
     * @param string $uf
     * @return PopCidadao
     */
    public function setUf($uf)
    {
        $this->uf = $uf;

        return $this;
    }

    /**
     * Get uf
     *
     * @return string 
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * Set ano
     *
     * @param integer $ano
     * @return PopCidadao
     */
    public function setAno($ano)
    {
        $this->ano = $ano;

        return $this;
    }

    /**
     * Get ano
     *
     * @return integer 
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return PopCidadao
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
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
