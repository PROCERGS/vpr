<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CedulaPop
 *
 * @ORM\Table(name="cedula_pop")
 * @ORM\Entity
 */
class CedulaPop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="nro_ordem", type="integer", nullable=false)
     */
    private $nroOrdem;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_corede", type="integer", nullable=false)
     */
    private $codCorede;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_ano", type="integer", nullable=true)
     */
    private $intAno;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_demanda", type="integer", nullable=true)
     */
    private $codDemanda;

    /**
     * @var integer
     *
     * @ORM\Column(name="cod_desc_cedula", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codDescCedula;



    /**
     * Set nroOrdem
     *
     * @param integer $nroOrdem
     * @return CedulaPop
     */
    public function setNroOrdem($nroOrdem)
    {
        $this->nroOrdem = $nroOrdem;

        return $this;
    }

    /**
     * Get nroOrdem
     *
     * @return integer 
     */
    public function getNroOrdem()
    {
        return $this->nroOrdem;
    }

    /**
     * Set codCorede
     *
     * @param integer $codCorede
     * @return CedulaPop
     */
    public function setCodCorede($codCorede)
    {
        $this->codCorede = $codCorede;

        return $this;
    }

    /**
     * Get codCorede
     *
     * @return integer 
     */
    public function getCodCorede()
    {
        return $this->codCorede;
    }

    /**
     * Set intAno
     *
     * @param integer $intAno
     * @return CedulaPop
     */
    public function setIntAno($intAno)
    {
        $this->intAno = $intAno;

        return $this;
    }

    /**
     * Get intAno
     *
     * @return integer 
     */
    public function getIntAno()
    {
        return $this->intAno;
    }

    /**
     * Set codDemanda
     *
     * @param integer $codDemanda
     * @return CedulaPop
     */
    public function setCodDemanda($codDemanda)
    {
        $this->codDemanda = $codDemanda;

        return $this;
    }

    /**
     * Get codDemanda
     *
     * @return integer 
     */
    public function getCodDemanda()
    {
        return $this->codDemanda;
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
}
