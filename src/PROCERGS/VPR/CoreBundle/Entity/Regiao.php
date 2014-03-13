<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Regiao
 *
 * @ORM\Table(name="regiao", indexes={@ORM\Index(name="fk_regiao_uf", columns={"id_uf"}), @ORM\Index(name="fk_regiao_tipo_regiao", columns={"id_tipo_regiao"})})
 * @ORM\Entity
 */
class Regiao
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_criacao", type="datetime", nullable=true)
     */
    private $dtCriacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_atualizacao", type="datetime", nullable=true)
     */
    private $dtAtualizacao;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_regiao", type="string", length=255, nullable=false)
     */
    private $nmRegiao;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_ordem", type="integer", nullable=true)
     */
    private $intOrdem;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_regiao", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRegiao;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Uf
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Uf")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_uf", referencedColumnName="id_uf")
     * })
     */
    private $idUf;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\TipoRegiao
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\TipoRegiao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_regiao", referencedColumnName="id_tipo_regiao")
     * })
     */
    private $idTipoRegiao;



    /**
     * Set dtCriacao
     *
     * @param \DateTime $dtCriacao
     * @return Regiao
     */
    public function setDtCriacao($dtCriacao)
    {
        $this->dtCriacao = $dtCriacao;

        return $this;
    }

    /**
     * Get dtCriacao
     *
     * @return \DateTime 
     */
    public function getDtCriacao()
    {
        return $this->dtCriacao;
    }

    /**
     * Set dtAtualizacao
     *
     * @param \DateTime $dtAtualizacao
     * @return Regiao
     */
    public function setDtAtualizacao($dtAtualizacao)
    {
        $this->dtAtualizacao = $dtAtualizacao;

        return $this;
    }

    /**
     * Get dtAtualizacao
     *
     * @return \DateTime 
     */
    public function getDtAtualizacao()
    {
        return $this->dtAtualizacao;
    }

    /**
     * Set nmRegiao
     *
     * @param string $nmRegiao
     * @return Regiao
     */
    public function setNmRegiao($nmRegiao)
    {
        $this->nmRegiao = $nmRegiao;

        return $this;
    }

    /**
     * Get nmRegiao
     *
     * @return string 
     */
    public function getNmRegiao()
    {
        return $this->nmRegiao;
    }

    /**
     * Set intOrdem
     *
     * @param integer $intOrdem
     * @return Regiao
     */
    public function setIntOrdem($intOrdem)
    {
        $this->intOrdem = $intOrdem;

        return $this;
    }

    /**
     * Get intOrdem
     *
     * @return integer 
     */
    public function getIntOrdem()
    {
        return $this->intOrdem;
    }

    /**
     * Set fgAtivo
     *
     * @param integer $fgAtivo
     * @return Regiao
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
     * Get idRegiao
     *
     * @return integer 
     */
    public function getIdRegiao()
    {
        return $this->idRegiao;
    }

    /**
     * Set idUf
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Uf $idUf
     * @return Regiao
     */
    public function setIdUf(\PROCERGS\VPR\CoreBundle\Entity\Uf $idUf = null)
    {
        $this->idUf = $idUf;

        return $this;
    }

    /**
     * Get idUf
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Uf 
     */
    public function getIdUf()
    {
        return $this->idUf;
    }

    /**
     * Set idTipoRegiao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\TipoRegiao $idTipoRegiao
     * @return Regiao
     */
    public function setIdTipoRegiao(\PROCERGS\VPR\CoreBundle\Entity\TipoRegiao $idTipoRegiao = null)
    {
        $this->idTipoRegiao = $idTipoRegiao;

        return $this;
    }

    /**
     * Get idTipoRegiao
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\TipoRegiao 
     */
    public function getIdTipoRegiao()
    {
        return $this->idTipoRegiao;
    }
}
