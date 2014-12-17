<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Voto
 *
 * @ORM\Table(name="legacy_voto", indexes={@ORM\Index(name="fk_voto_votacao_meio_votacao", columns={"id_meio_votacao"}), @ORM\Index(name="fk_voto_votacao_grupo_demanda", columns={"id_municipio"}), @ORM\Index(name="fk_voto_cedula", columns={"id_cedula"})})
 * @ORM\Entity
 */
class LegacyVoto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_voto", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVoto;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cedula", type="integer", nullable=false)
     */
    private $idCedula;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_municipio", type="integer", nullable=false)
     */
    private $idMunicipio;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_meio_votacao", type="integer", nullable=false)
     */
    private $idMeioVotacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dth_voto", type="datetime", nullable=false)
     */
    private $dthVoto;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_ip_inc", type="string", length=20, nullable=false)
     */
    private $nroIpInc;



    /**
     * Set idCedula
     *
     * @param integer $idCedula
     * @return Voto
     */
    public function setIdCedula($idCedula)
    {
        $this->idCedula = $idCedula;

        return $this;
    }

    /**
     * Get idCedula
     *
     * @return integer 
     */
    public function getIdCedula()
    {
        return $this->idCedula;
    }

    /**
     * Set idMunicipio
     *
     * @param integer $idMunicipio
     * @return Voto
     */
    public function setIdMunicipio($idMunicipio)
    {
        $this->idMunicipio = $idMunicipio;

        return $this;
    }

    /**
     * Get idMunicipio
     *
     * @return integer 
     */
    public function getIdMunicipio()
    {
        return $this->idMunicipio;
    }

    /**
     * Set idMeioVotacao
     *
     * @param integer $idMeioVotacao
     * @return Voto
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
     * Set dthVoto
     *
     * @param \DateTime $dthVoto
     * @return Voto
     */
    public function setDthVoto($dthVoto)
    {
        $this->dthVoto = $dthVoto;

        return $this;
    }

    /**
     * Get dthVoto
     *
     * @return \DateTime 
     */
    public function getDthVoto()
    {
        return $this->dthVoto;
    }

    /**
     * Set nroIpInc
     *
     * @param string $nroIpInc
     * @return Voto
     */
    public function setNroIpInc($nroIpInc)
    {
        $this->nroIpInc = $nroIpInc;

        return $this;
    }

    /**
     * Get nroIpInc
     *
     * @return string 
     */
    public function getNroIpInc()
    {
        return $this->nroIpInc;
    }

    /**
     * Get idVoto
     *
     * @return integer 
     */
    public function getIdVoto()
    {
        return $this->idVoto;
    }
}
