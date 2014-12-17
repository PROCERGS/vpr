<?php

namespace PROCERGS\VPR\CoreBundle\Entity\WorldBank;

use Doctrine\ORM\Mapping as ORM;

/**
 * LegacyPerson
 *
 * @ORM\Table(name="wb_legacy_person", indexes={
 *      @ORM\Index(name="idx_legacy_voter_registration", columns={"voter_registration"})
 * })
 * @ORM\Entity
 */
class LegacyPerson
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="voter_registration", type="string", length=12)
     */
    private $voterRegistration;

    /**
     * @var integer
     *
     * @ORM\Column(name="treatment", type="integer")
     */
    private $treatment;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set voterRegistration
     *
     * @param string $voterRegistration
     * @return LegacyPerson
     */
    public function setVoterRegistration($voterRegistration)
    {
        $this->voterRegistration = $voterRegistration;

        return $this;
    }

    /**
     * Get voterRegistration
     *
     * @return string
     */
    public function getVoterRegistration()
    {
        return $this->voterRegistration;
    }

    /**
     * Set treatment
     *
     * @param integer $treatment
     * @return LegacyPerson
     */
    public function setTreatment($treatment)
    {
        $this->treatment = $treatment;

        return $this;
    }

    /**
     * Get treatment
     *
     * @return integer
     */
    public function getTreatment()
    {
        return $this->treatment;
    }
}
