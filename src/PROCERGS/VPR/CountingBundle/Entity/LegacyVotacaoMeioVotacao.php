<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VotacaoMeioVotacao
 *
 * @ORM\Table(name="legacy_votacao_meio_votacao", indexes={@ORM\Index(name="fk_votacao_meio_votacao_votacao", columns={"id_votacao"}), @ORM\Index(name="fk_votacao_meio_votacao_meio_votacao", columns={"id_meio_votacao"})})
 * @ORM\Entity
 */
class LegacyVotacaoMeioVotacao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao_meio_votacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotacaoMeioVotacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao", type="integer", nullable=false)
     */
    private $idVotacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_meio_votacao", type="integer", nullable=false)
     */
    private $idMeioVotacao;

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
     * Set idVotacao
     *
     * @param integer $idVotacao
     * @return VotacaoMeioVotacao
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
     * Set idMeioVotacao
     *
     * @param integer $idMeioVotacao
     * @return VotacaoMeioVotacao
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
}
