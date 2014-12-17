<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Situacao
 *
 * @ORM\Table(name="legacy_situacao")
 * @ORM\Entity
 */
class LegacySituacao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_situacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSituacao;

    /**
     * @var string
     *
     * @ORM\Column(name="nm_situacao", type="string", length=45, nullable=false)
     */
    private $nmSituacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;



    /**
     * Set nmSituacao
     *
     * @param string $nmSituacao
     * @return Situacao
     */
    public function setNmSituacao($nmSituacao)
    {
        $this->nmSituacao = $nmSituacao;

        return $this;
    }

    /**
     * Get nmSituacao
     *
     * @return string 
     */
    public function getNmSituacao()
    {
        return $this->nmSituacao;
    }

    /**
     * Set fgAtivo
     *
     * @param integer $fgAtivo
     * @return Situacao
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
     * Get idSituacao
     *
     * @return integer 
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }
}
