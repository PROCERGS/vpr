<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VotacaoGrupoDemanda
 *
 * @ORM\Table(name="legacy_votacao_grupo_demanda", indexes={@ORM\Index(name="fk_votacao_grupo_demanda_grupo_demanda", columns={"id_grupo_demanda"}), @ORM\Index(name="fk_votacao_grupo_demanda_votacao", columns={"id_votacao"})})
 * @ORM\Entity
 */
class LegacyVotacaoGrupoDemanda
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao_grupo_demanda", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVotacaoGrupoDemanda;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_grupo_demanda", type="integer", nullable=false)
     */
    private $idGrupoDemanda;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_votacao", type="integer", nullable=false)
     */
    private $idVotacao;

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
    private $sequencia = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="fg_titulo_simples", type="string", nullable=false)
     */
    private $fgTituloSimples = '0';



    /**
     * Set idGrupoDemanda
     *
     * @param integer $idGrupoDemanda
     * @return VotacaoGrupoDemanda
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
     * Set idVotacao
     *
     * @param integer $idVotacao
     * @return VotacaoGrupoDemanda
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
}
