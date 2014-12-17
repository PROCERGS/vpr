<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VotoLog
 *
 * @ORM\Table(name="legacy_voto_log", indexes={@ORM\Index(name="fk_voto_log_cidadao", columns={"id_cidadao"}), @ORM\Index(name="fk_voto_log_votacao", columns={"id_votacao"}), @ORM\Index(name="fk_voto_log_grupo_demanda", columns={"id_grupo_demanda"}), @ORM\Index(name="fk_voto_log_meio_votacao", columns={"id_meio_votacao"})})
 * @ORM\Entity
 */
class LegacyVotoLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_voto_log", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotoLog;

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
     * @var integer
     *
     * @ORM\Column(name="id_grupo_demanda", type="integer", nullable=false)
     */
    private $idGrupoDemanda;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_meio_votacao", type="integer", nullable=false)
     */
    private $idMeioVotacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_inicio", type="datetime", nullable=false)
     */
    private $dthInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_fim", type="datetime", nullable=true)
     */
    private $dthFim;

    /**
     * @var integer
     *
     * @ORM\Column(name="qtd_selecoes", type="integer", nullable=true)
     */
    private $qtdSelecoes = '0';

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
     * @return VotoLog
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
     * @return VotoLog
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
     * Set idGrupoDemanda
     *
     * @param integer $idGrupoDemanda
     * @return VotoLog
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
     * Set idMeioVotacao
     *
     * @param integer $idMeioVotacao
     * @return VotoLog
     */
    public function setIdMeioVotacao($idMeioVotacao)
    {
        $this->idMeioVotacao = $idMeioVotacao;

        return $this;
    }

    /**
     * Get idMeioVotacao
     *
     * @return integer 
     */
    public function getIdMeioVotacao()
    {
        return $this->idMeioVotacao;
    }

    /**
     * Set dthInicio
     *
     * @param \DateTime $dthInicio
     * @return VotoLog
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
     * @return VotoLog
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
     * @param integer $qtdSelecoes
     * @return VotoLog
     */
    public function setQtdSelecoes($qtdSelecoes)
    {
        $this->qtdSelecoes = $qtdSelecoes;

        return $this;
    }

    /**
     * Get qtdSelecoes
     *
     * @return integer 
     */
    public function getQtdSelecoes()
    {
        return $this->qtdSelecoes;
    }

    /**
     * Set nroIp
     *
     * @param string $nroIp
     * @return VotoLog
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
