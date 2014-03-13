<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VotacaoMeioVotacao
 *
 * @ORM\Table(name="votacao_meio_votacao", indexes={@ORM\Index(name="fk_votacao_meio_votacao_votacao", columns={"id_votacao"}), @ORM\Index(name="fk_votacao_meio_votacao_meio_votacao", columns={"id_meio_votacao"})})
 * @ORM\Entity
 */
class VotacaoMeioVotacao
{
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
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao_meio_votacao", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotacaoMeioVotacao;

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
     * Set dthInicio
     *
     * @param \DateTime $dthInicio
     * @return VotacaoMeioVotacao
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
     * @return VotacaoMeioVotacao
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
     * Set fgAtivo
     *
     * @param integer $fgAtivo
     * @return VotacaoMeioVotacao
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
     * Get idVotacaoMeioVotacao
     *
     * @return integer 
     */
    public function getIdVotacaoMeioVotacao()
    {
        return $this->idVotacaoMeioVotacao;
    }

    /**
     * Set idVotacao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Votacao $idVotacao
     * @return VotacaoMeioVotacao
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
     * @return VotacaoMeioVotacao
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
}
