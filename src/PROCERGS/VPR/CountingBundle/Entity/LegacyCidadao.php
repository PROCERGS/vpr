<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cidadao
 *
 * @ORM\Table(name="legacy_cidadao", uniqueConstraints={@ORM\UniqueConstraint(name="nro_titulo_UNIQUE", columns={"nro_titulo"}), @ORM\UniqueConstraint(name="rg_UNIQUE", columns={"rg"})}, indexes={@ORM\Index(name="fk_cidadao_municipio", columns={"id_municipio"})})
 * @ORM\Entity
 */
class LegacyCidadao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cidadao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCidadao;

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
     * @ORM\Column(name="cpf", type="bigint", nullable=true)
     */
    private $cpf;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_email", type="string", length=100, nullable=true)
     */
    private $dsEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_telefone", type="string", length=45, nullable=true)
     */
    private $nroTelefone;

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
     * @var integer
     *
     * @ORM\Column(name="id_municipio", type="integer", nullable=false)
     */
    private $idMunicipio;



    /**
     * Set nroTitulo
     *
     * @param integer $nroTitulo
     * @return Cidadao
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
     * @return Cidadao
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
     * Set cpf
     *
     * @param integer $cpf
     * @return Cidadao
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get cpf
     *
     * @return integer 
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Set dsEmail
     *
     * @param string $dsEmail
     * @return Cidadao
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
     * Set nroTelefone
     *
     * @param string $nroTelefone
     * @return Cidadao
     */
    public function setNroTelefone($nroTelefone)
    {
        $this->nroTelefone = $nroTelefone;

        return $this;
    }

    /**
     * Get nroTelefone
     *
     * @return string 
     */
    public function getNroTelefone()
    {
        return $this->nroTelefone;
    }

    /**
     * Set ctrDthInc
     *
     * @param \DateTime $ctrDthInc
     * @return Cidadao
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
     * @return Cidadao
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
     * Set idMunicipio
     *
     * @param integer $idMunicipio
     * @return Cidadao
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
     * Get idCidadao
     *
     * @return integer 
     */
    public function getIdCidadao()
    {
        return $this->idCidadao;
    }
}
