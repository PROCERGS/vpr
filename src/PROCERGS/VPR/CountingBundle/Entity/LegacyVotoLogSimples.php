<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VotoLogSimples
 *
 * @ORM\Table(name="legacy_voto_log_simples")
 * @ORM\Entity
 */
class LegacyVotoLogSimples
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_voto_log", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotoLog = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cidadao", type="integer", nullable=false)
     */
    private $idCidadao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao", type="integer", nullable=false)
     */
    private $idVotacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_inicio", type="datetime", nullable=true)
     */
    private $dthInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_fim", type="datetime", nullable=true)
     */
    private $dthFim;

    /**
     * @var string
     *
     * @ORM\Column(name="qtd_selecoes", type="decimal", precision=32, scale=0, nullable=true)
     */
    private $qtdSelecoes;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_ip", type="string", length=15, nullable=false)
     */
    private $nroIp;



    /**
     * Set idCidadao
     *
     * @param integer $idCidadao
     * @return VotoLogSimples
     */
    public function setIdCidadao($idCidadao)
    {
        $this->idCidadao = $idCidadao;

        return $this;
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
     * Set idVotacao
     *
     * @param integer $idVotacao
     * @return VotoLogSimples
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
     * Set dthInicio
     *
     * @param \DateTime $dthInicio
     * @return VotoLogSimples
     */
    public function setDthInicio($dthInicio)
    {
        $this->dthInicio = $dthInicio;

        return $this;
    }

    /**
     * Get dthInicio
     *
     * @return \DateTime 
     */
    public function getDthInicio()
    {
        return $this->dthInicio;
    }

    /**
     * Set dthFim
     *
     * @param \DateTime $dthFim
     * @return VotoLogSimples
     */
    public function setDthFim($dthFim)
    {
        $this->dthFim = $dthFim;

        return $this;
    }

    /**
     * Get dthFim
     *
     * @return \DateTime 
     */
    public function getDthFim()
    {
        return $this->dthFim;
    }

    /**
     * Set qtdSelecoes
     *
     * @param string $qtdSelecoes
     * @return VotoLogSimples
     */
    public function setQtdSelecoes($qtdSelecoes)
    {
        $this->qtdSelecoes = $qtdSelecoes;

        return $this;
    }

    /**
     * Get qtdSelecoes
     *
     * @return string 
     */
    public function getQtdSelecoes()
    {
        return $this->qtdSelecoes;
    }

    /**
     * Set nroIp
     *
     * @param string $nroIp
     * @return VotoLogSimples
     */
    public function setNroIp($nroIp)
    {
        $this->nroIp = $nroIp;

        return $this;
    }

    /**
     * Get nroIp
     *
     * @return string 
     */
    public function getNroIp()
    {
        return $this->nroIp;
    }

    /**
     * Get idVotoLog
     *
     * @return integer 
     */
    public function getIdVotoLog()
    {
        return $this->idVotoLog;
    }
}
