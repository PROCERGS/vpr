<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoRegiao
 *
 * @ORM\Table(name="legacy_tipo_regiao")
 * @ORM\Entity
 */
class LegacyTipoRegiao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_regiao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTipoRegiao;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_tipo_regiao", type="string", length=255, nullable=false)
     */
    private $nmTipoRegiao;

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
     * Set nmTipoRegiao
     *
     * @param string $nmTipoRegiao
     * @return TipoRegiao
     */
    public function setNmTipoRegiao($nmTipoRegiao)
    {
        $this->nmTipoRegiao = $nmTipoRegiao;

        return $this;
    }

    /**
     * Get nmTipoRegiao
     *
     * @return string 
     */
    public function getNmTipoRegiao()
    {
        return $this->nmTipoRegiao;
    }

    /**
     * Set intOrdem
     *
     * @param integer $intOrdem
     * @return TipoRegiao
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
     * @return TipoRegiao
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
     * Get idTipoRegiao
     *
     * @return integer 
     */
    public function getIdTipoRegiao()
    {
        return $this->idTipoRegiao;
    }
}
