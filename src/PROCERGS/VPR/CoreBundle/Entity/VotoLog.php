<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VotoLog
 *
 * @ORM\Table(name="voto_log", indexes={@ORM\Index(name="fk_voto_log_cidadao", columns={"id_cidadao"}), @ORM\Index(name="fk_voto_log_votacao", columns={"id_votacao"}), @ORM\Index(name="fk_voto_log_grupo_demanda", columns={"id_grupo_demanda"}), @ORM\Index(name="fk_voto_log_meio_votacao", columns={"id_meio_votacao"})})
 * @ORM\Entity
 */
class VotoLog
{
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
    private $qtdSelecoes;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_ip", type="string", length=15, nullable=false)
     */
    private $nroIp;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_voto_log", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotoLog;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Votacao
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Votacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_votacao", referencedColumnName="id_votacao")
     * })
     */
    private $idVotacao;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\MeioVotacao
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\MeioVotacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_meio_votacao", referencedColumnName="id_meio_votacao")
     * })
     */
    private $idMeioVotacao;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_grupo_demanda", referencedColumnName="id_grupo_demanda")
     * })
     */
    private $idGrupoDemanda;

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

    /**
     * Set idVotacao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Votacao $idVotacao
     * @return VotoLog
     */
    public function setIdVotacao(\PROCERGS\VPR\CoreBundle\Entity\Votacao $idVotacao = null)
    {
        $this->idVotacao = $idVotacao;

        return $this;
    }

    /**
     * Get idVotacao
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Votacao 
     */
    public function getIdVotacao()
    {
        return $this->idVotacao;
    }

    /**
     * Set idMeioVotacao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\MeioVotacao $idMeioVotacao
     * @return VotoLog
     */
    public function setIdMeioVotacao(\PROCERGS\VPR\CoreBundle\Entity\MeioVotacao $idMeioVotacao = null)
    {
        $this->idMeioVotacao = $idMeioVotacao;

        return $this;
    }

    /**
     * Get idMeioVotacao
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\MeioVotacao 
     */
    public function getIdMeioVotacao()
    {
        return $this->idMeioVotacao;
    }

    /**
     * Set idGrupoDemanda
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda $idGrupoDemanda
     * @return VotoLog
     */
    public function setIdGrupoDemanda(\PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda $idGrupoDemanda = null)
    {
        $this->idGrupoDemanda = $idGrupoDemanda;

        return $this;
    }

    /**
     * Get idGrupoDemanda
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda 
     */
    public function getIdGrupoDemanda()
    {
        return $this->idGrupoDemanda;
    }

    /**
     * Set idCidadao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Cidadao $idCidadao
     * @return VotoLog
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
