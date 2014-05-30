<?php

namespace PROCERGS\VPR\CoreBundle\Entity\WorldBank;

use Doctrine\ORM\Mapping as ORM;

/**
 * GabineteDigitalPerson
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GabineteDigitalPerson
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
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

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
     * Set email
     *
     * @param string $email
     * @return GabineteDigitalPerson
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set treatment
     *
     * @param integer $treatment
     * @return GabineteDigitalPerson
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
