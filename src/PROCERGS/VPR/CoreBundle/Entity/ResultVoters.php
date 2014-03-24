<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultVoters
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ResultVoters
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
     * @ORM\Column(name="signature", type="string", length=255)
     */
    private $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="authType", type="string", length=255)
     */
    private $authType;


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
     * Set signature
     *
     * @param string $signature
     * @return ResultVoters
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string 
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set authType
     *
     * @param string $authType
     * @return ResultVoters
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;

        return $this;
    }

    /**
     * Get authType
     *
     * @return string 
     */
    public function getAuthType()
    {
        return $this->authType;
    }
}
