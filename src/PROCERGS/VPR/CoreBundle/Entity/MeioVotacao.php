<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeioVotacao
 *
 * @ORM\Table(name="meio_votacao")
 * @ORM\Entity
 */
class MeioVotacao
{
    /**
     * @var string
     *
     * @ORM\Column(name="nm_meio_votacao", type="string", length=45, nullable=false)
     */
    private $nmMeioVotacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="fg_ativo", type="integer", nullable=true)
     */
    private $fgAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_meio_votacao", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMeioVotacao;



    /**
     * Set nmMeioVotacao
     *
     * @param string $nmMeioVotacao
     * @return MeioVotacao
     */
    public function setNmMeioVotacao($nmMeioVotacao)
    {
        $this->nmMeioVotacao = $nmMeioVotacao;

        return $this;
    }

    /**
     * Get nmMeioVotacao
     *
     * @return string 
     */
    public function getNmMeioVotacao()
    {
        return $this->nmMeioVotacao;
    }

    /**
     * Set fgAtivo
     *
     * @param integer $fgAtivo
     * @return MeioVotacao
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
     * Get idMeioVotacao
     *
     * @return integer 
     */
    public function getIdMeioVotacao()
    {
        return $this->idMeioVotacao;
    }
}
