<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PopVotoWeb
 *
 * @ORM\Table(name="legacy_pop_voto_web")
 * @ORM\Entity
 */
class LegacyPopVotoWeb
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_desc_cedula", type="integer", nullable=false)
     */
    private $codDescCedula;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ctr_dth_inc", type="datetime", nullable=false)
     */
    private $ctrDthInc;

    /**
     * @var integer
     *
     * @ORM\Column(name="ind_branco", type="integer", nullable=false)
     */
    private $indBranco = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_municipio", type="integer", nullable=true)
     */
    private $codMunicipio;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_ano_corrente", type="integer", nullable=true)
     */
    private $nroAnoCorrente = '2013';



    /**
     * Set codDescCedula
     *
     * @param integer $codDescCedula
     * @return PopVotoWeb
     */
    public function setCodDescCedula($codDescCedula)
    {
        $this->codDescCedula = $codDescCedula;

        return $this;
    }

    /**
     * Get codDescCedula
     *
     * @return integer 
     */
    public function getCodDescCedula()
    {
        return $this->codDescCedula;
    }

    /**
     * Set ctrDthInc
     *
     * @param \DateTime $ctrDthInc
     * @return PopVotoWeb
     */
    public function setCtrDthInc($ctrDthInc)
    {
        $this->ctrDthInc = $ctrDthInc;

        return $this;
    }

    /**
     * Get ctrDthInc
     *
     * @return \DateTime 
     */
    public function getCtrDthInc()
    {
        return $this->ctrDthInc;
    }

    /**
     * Set indBranco
     *
     * @param integer $indBranco
     * @return PopVotoWeb
     */
    public function setIndBranco($indBranco)
    {
        $this->indBranco = $indBranco;

        return $this;
    }

    /**
     * Get indBranco
     *
     * @return integer 
     */
    public function getIndBranco()
    {
        return $this->indBranco;
    }

    /**
     * Set codMunicipio
     *
     * @param integer $codMunicipio
     * @return PopVotoWeb
     */
    public function setCodMunicipio($codMunicipio)
    {
        $this->codMunicipio = $codMunicipio;

        return $this;
    }

    /**
     * Get codMunicipio
     *
     * @return integer 
     */
    public function getCodMunicipio()
    {
        return $this->codMunicipio;
    }

    /**
     * Set nroAnoCorrente
     *
     * @param integer $nroAnoCorrente
     * @return PopVotoWeb
     */
    public function setNroAnoCorrente($nroAnoCorrente)
    {
        $this->nroAnoCorrente = $nroAnoCorrente;

        return $this;
    }

    /**
     * Get nroAnoCorrente
     *
     * @return integer 
     */
    public function getNroAnoCorrente()
    {
        return $this->nroAnoCorrente;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
