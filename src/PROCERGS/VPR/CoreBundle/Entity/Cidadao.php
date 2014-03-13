<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cidadao
 *
 * @ORM\Table(name="cidadao", uniqueConstraints={@ORM\UniqueConstraint(name="nro_titulo_UNIQUE", columns={"nro_titulo"}), @ORM\UniqueConstraint(name="rg_UNIQUE", columns={"rg"}), @ORM\UniqueConstraint(name="cpf_UNIQUE", columns={"cpf"})}, indexes={@ORM\Index(name="fk_cidadao_municipio", columns={"id_municipio"})})
 * @ORM\Entity
 */
class Cidadao
{
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
     * @ORM\Column(name="id_cidadao", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCidadao;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Municipio
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_municipio", referencedColumnName="id_municipio")
     * })
     */
    private $idMunicipio;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\EleitorTre2012
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\EleitorTre2012")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nro_titulo", referencedColumnName="nro_titulo")
     * })
     */
    private $nroTitulo;



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
     * Get idCidadao
     *
     * @return integer 
     */
    public function getIdCidadao()
    {
        return $this->idCidadao;
    }

    /**
     * Set idMunicipio
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Municipio $idMunicipio
     * @return Cidadao
     */
    public function setIdMunicipio(\PROCERGS\VPR\CoreBundle\Entity\Municipio $idMunicipio = null)
    {
        $this->idMunicipio = $idMunicipio;

        return $this;
    }

    /**
     * Get idMunicipio
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Municipio 
     */
    public function getIdMunicipio()
    {
        return $this->idMunicipio;
    }

    /**
     * Set nroTitulo
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\EleitorTre2012 $nroTitulo
     * @return Cidadao
     */
    public function setNroTitulo(\PROCERGS\VPR\CoreBundle\Entity\EleitorTre2012 $nroTitulo = null)
    {
        $this->nroTitulo = $nroTitulo;

        return $this;
    }

    /**
     * Get nroTitulo
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\EleitorTre2012 
     */
    public function getNroTitulo()
    {
        return $this->nroTitulo;
    }
}
