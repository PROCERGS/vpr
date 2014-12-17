<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Votacao
 *
 * @ORM\Table(name="legacy_votacao", indexes={@ORM\Index(name="fk_votacao_situacao", columns={"id_situacao"})})
 * @ORM\Entity
 */
class LegacyVotacao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_situacao", type="integer", nullable=false)
     */
    private $idSituacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_exercicio", type="integer", nullable=true)
     */
    private $intExercicio;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_votacao", type="string", length=60, nullable=false)
     */
    private $nmVotacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_inicio", type="datetime", nullable=false)
     */
    private $dthInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_fim", type="datetime", nullable=false)
     */
    private $dthFim;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;



    /**
     * Set idSituacao
     *
     * @param integer $idSituacao
     * @return Votacao
     */
    public function setIdSituacao($idSituacao)
    {
        $this->idSituacao = $idSituacao;

        return $this;
    }

    /**
     * Get idSituacao
     *
     * @return integer 
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }

    /**
     * Set intExercicio
     *
     * @param integer $intExercicio
     * @return Votacao
     */
    public function setIntExercicio($intExercicio)
    {
        $this->intExercicio = $intExercicio;

        return $this;
    }

    /**
     * Get intExercicio
     *
     * @return integer 
     */
    public function getIntExercicio()
    {
        return $this->intExercicio;
    }

    /**
     * Set nmVotacao
     *
     * @param string $nmVotacao
     * @return Votacao
     */
    public function setNmVotacao($nmVotacao)
    {
        $this->nmVotacao = $nmVotacao;

        return $this;
    }

    /**
     * Get nmVotacao
     *
     * @return string 
     */
    public function getNmVotacao()
    {
        return $this->nmVotacao;
    }

    /**
     * Set dthInicio
     *
     * @param \DateTime $dthInicio
     * @return Votacao
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
     * @return Votacao
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
     * @return Votacao
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
     * Get idVotacao
     *
     * @return integer 
     */
    public function getIdVotacao()
    {
        return $this->idVotacao;
    }
}
