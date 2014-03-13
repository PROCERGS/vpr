<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogErros
 *
 * @ORM\Table(name="log_erros", indexes={@ORM\Index(name="fk_log_erros_cidadao1", columns={"id_cidadao"})})
 * @ORM\Entity
 */
class LogErros
{
    /**
     * @var integer
     *
     * @ORM\Column(name="nro_titulo", type="bigint", nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="ds_erro", type="string", length=100, nullable=true)
     */
    private $dsErro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_inc", type="datetime", nullable=true)
     */
    private $dthInc;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_ip_inc", type="string", length=20, nullable=true)
     */
    private $nroIpInc;

    /**
     * @var string
     *
     * @ORM\Column(name="dump", type="text", nullable=true)
     */
    private $dump;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_log_erros", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idLogErros;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Cidadao
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Cidadao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cidadao", referencedColumnName="id_cidadao")
     * })
     */
    private $idCidadao;



    /**
     * Set nroTitulo
     *
     * @param integer $nroTitulo
     * @return LogErros
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
     * @return LogErros
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
     * @return LogErros
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
     * @return LogErros
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
     * @return LogErros
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
     * Set dsErro
     *
     * @param string $dsErro
     * @return LogErros
     */
    public function setDsErro($dsErro)
    {
        $this->dsErro = $dsErro;

        return $this;
    }

    /**
     * Get dsErro
     *
     * @return string 
     */
    public function getDsErro()
    {
        return $this->dsErro;
    }

    /**
     * Set dthInc
     *
     * @param \DateTime $dthInc
     * @return LogErros
     */
    public function setDthInc($dthInc)
    {
        $this->dthInc = $dthInc;

        return $this;
    }

    /**
     * Get dthInc
     *
     * @return \DateTime 
     */
    public function getDthInc()
    {
        return $this->dthInc;
    }

    /**
     * Set nroIpInc
     *
     * @param string $nroIpInc
     * @return LogErros
     */
    public function setNroIpInc($nroIpInc)
    {
        $this->nroIpInc = $nroIpInc;

        return $this;
    }

    /**
     * Get nroIpInc
     *
     * @return string 
     */
    public function getNroIpInc()
    {
        return $this->nroIpInc;
    }

    /**
     * Set dump
     *
     * @param string $dump
     * @return LogErros
     */
    public function setDump($dump)
    {
        $this->dump = $dump;

        return $this;
    }

    /**
     * Get dump
     *
     * @return string 
     */
    public function getDump()
    {
        return $this->dump;
    }

    /**
     * Get idLogErros
     *
     * @return integer 
     */
    public function getIdLogErros()
    {
        return $this->idLogErros;
    }

    /**
     * Set idCidadao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Cidadao $idCidadao
     * @return LogErros
     */
    public function setIdCidadao(\PROCERGS\VPR\CoreBundle\Entity\Cidadao $idCidadao = null)
    {
        $this->idCidadao = $idCidadao;

        return $this;
    }

    /**
     * Get idCidadao
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Cidadao 
     */
    public function getIdCidadao()
    {
        return $this->idCidadao;
    }
}
