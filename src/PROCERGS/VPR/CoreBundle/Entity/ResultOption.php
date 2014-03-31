<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultOption
 *
 * @ORM\Table(name="result_option")
 * @ORM\Entity
 */
class ResultOption
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
     * @ORM\Column(name="auth_type", type="string", length=255)
     */
    private $authType;

    /**
     * @var string
     *
     * @ORM\Column(name="voter_tegistration", type="string", length=12)
     */
    private $voterRegistration;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_null", type="boolean")
     */
    private $isNull;

    /**
     * @var integer
     *
     * @ORM\Column(name="votes", type="bigint")
     */
    private $votes;


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
     * Set authType
     *
     * @param string $authType
     * @return ResultOption
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

    /**
     * Set voterRegistration
     *
     * @param string $voterRegistration
     * @return ResultOption
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
     * Set isNull
     *
     * @param boolean $isNull
     * @return ResultOption
     */
    public function setIsNull($isNull)
    {
        $this->isNull = $isNull;

        return $this;
    }

    /**
     * Get isNull
     *
     * @return boolean 
     */
    public function getIsNull()
    {
        return $this->isNull;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     * @return ResultOption
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return integer 
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
