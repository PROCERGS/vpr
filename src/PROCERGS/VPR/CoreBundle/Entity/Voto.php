<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Voto
 *
 * @ORM\Table(name="voto", indexes={@ORM\Index(name="fk_voto_votacao_meio_votacao", columns={"id_meio_votacao"}), @ORM\Index(name="fk_voto_votacao_grupo_demanda", columns={"id_municipio"}), @ORM\Index(name="fk_voto_cedula", columns={"id_cedula"})})
 * @ORM\Entity
 */
class Voto
{
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
     * @var integer
     *
     * @ORM\Column(name="id_voto", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVoto;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Municipio
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_municipio", referencedColumnName="id_municipio")
     * })
     */
    private $idMunicipio;

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
     * @var \PROCERGS\VPR\CoreBundle\Entity\CedulaOld
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\CedulaOld")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cedula", referencedColumnName="id_cedula")
     * })
     */
    private $idCedula;



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

    /**
     * Set idMunicipio
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Municipio $idMunicipio
     * @return Voto
     */
    public function setIdMunicipio(\PROCERGS\VPR\CoreBundle\Entity\Municipio $idMunicipio = null)
    {
        $this->idMunicipio = $idMunicipio;

        return $this;
    }

    /**
     * Get idMunicipio
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Municipio 
     */
    public function getIdMunicipio()
    {
        return $this->idMunicipio;
    }

    /**
     * Set idMeioVotacao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\MeioVotacao $idMeioVotacao
     * @return Voto
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

    /**
     * Set idCedula
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\CedulaOld $idCedula
     * @return Voto
     */
    public function setIdCedula(\PROCERGS\VPR\CoreBundle\Entity\CedulaOld $idCedula = null)
    {
        $this->idCedula = $idCedula;

        return $this;
    }

    /**
     * Get idCedula
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\CedulaOld 
     */
    public function getIdCedula()
    {
        return $this->idCedula;
    }
}
