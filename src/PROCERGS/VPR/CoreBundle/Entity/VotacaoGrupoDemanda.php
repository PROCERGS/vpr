<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VotacaoGrupoDemanda
 *
 * @ORM\Table(name="votacao_grupo_demanda", indexes={@ORM\Index(name="fk_votacao_grupo_demanda_grupo_demanda", columns={"id_grupo_demanda"}), @ORM\Index(name="fk_votacao_grupo_demanda_votacao", columns={"id_votacao"})})
 * @ORM\Entity
 */
class VotacaoGrupoDemanda
{
    /**
     * @var integer
     *
     * @ORM\Column(name="nro_inicial", type="integer", nullable=true)
     */
    private $nroInicial;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_final", type="integer", nullable=true)
     */
    private $nroFinal;

    /**
     * @var integer
     *
     * @ORM\Column(name="qtd_max_item", type="integer", nullable=true)
     */
    private $qtdMaxItem;

    /**
     * @var integer
     *
     * @ORM\Column(name="qtd_max_escolha", type="integer", nullable=true)
     */
    private $qtdMaxEscolha;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequencia", type="integer", nullable=false)
     */
    private $sequencia;

    /**
     * @var string
     *
     * @ORM\Column(name="fg_titulo_simples", type="string", nullable=false)
     */
    private $fgTituloSimples;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao_grupo_demanda", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotacaoGrupoDemanda;

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
     * @var \PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_grupo_demanda", referencedColumnName="id_grupo_demanda")
     * })
     */
    private $idGrupoDemanda;



    /**
     * Set nroInicial
     *
     * @param integer $nroInicial
     * @return VotacaoGrupoDemanda
     */
    public function setNroInicial($nroInicial)
    {
        $this->nroInicial = $nroInicial;

        return $this;
    }

    /**
     * Get nroInicial
     *
     * @return integer 
     */
    public function getNroInicial()
    {
        return $this->nroInicial;
    }

    /**
     * Set nroFinal
     *
     * @param integer $nroFinal
     * @return VotacaoGrupoDemanda
     */
    public function setNroFinal($nroFinal)
    {
        $this->nroFinal = $nroFinal;

        return $this;
    }

    /**
     * Get nroFinal
     *
     * @return integer 
     */
    public function getNroFinal()
    {
        return $this->nroFinal;
    }

    /**
     * Set qtdMaxItem
     *
     * @param integer $qtdMaxItem
     * @return VotacaoGrupoDemanda
     */
    public function setQtdMaxItem($qtdMaxItem)
    {
        $this->qtdMaxItem = $qtdMaxItem;

        return $this;
    }

    /**
     * Get qtdMaxItem
     *
     * @return integer 
     */
    public function getQtdMaxItem()
    {
        return $this->qtdMaxItem;
    }

    /**
     * Set qtdMaxEscolha
     *
     * @param integer $qtdMaxEscolha
     * @return VotacaoGrupoDemanda
     */
    public function setQtdMaxEscolha($qtdMaxEscolha)
    {
        $this->qtdMaxEscolha = $qtdMaxEscolha;

        return $this;
    }

    /**
     * Get qtdMaxEscolha
     *
     * @return integer 
     */
    public function getQtdMaxEscolha()
    {
        return $this->qtdMaxEscolha;
    }

    /**
     * Set fgAtivo
     *
     * @param integer $fgAtivo
     * @return VotacaoGrupoDemanda
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
     * Set sequencia
     *
     * @param integer $sequencia
     * @return VotacaoGrupoDemanda
     */
    public function setSequencia($sequencia)
    {
        $this->sequencia = $sequencia;

        return $this;
    }

    /**
     * Get sequencia
     *
     * @return integer 
     */
    public function getSequencia()
    {
        return $this->sequencia;
    }

    /**
     * Set fgTituloSimples
     *
     * @param string $fgTituloSimples
     * @return VotacaoGrupoDemanda
     */
    public function setFgTituloSimples($fgTituloSimples)
    {
        $this->fgTituloSimples = $fgTituloSimples;

        return $this;
    }

    /**
     * Get fgTituloSimples
     *
     * @return string 
     */
    public function getFgTituloSimples()
    {
        return $this->fgTituloSimples;
    }

    /**
     * Get idVotacaoGrupoDemanda
     *
     * @return integer 
     */
    public function getIdVotacaoGrupoDemanda()
    {
        return $this->idVotacaoGrupoDemanda;
    }

    /**
     * Set idVotacao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Votacao $idVotacao
     * @return VotacaoGrupoDemanda
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
     * Set idGrupoDemanda
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\GrupoDemanda $idGrupoDemanda
     * @return VotacaoGrupoDemanda
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
}
