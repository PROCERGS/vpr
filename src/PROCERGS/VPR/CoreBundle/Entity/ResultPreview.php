<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultPreview
 *
 * @ORM\Table(name="result_preview")
 * @ORM\Entity
 */
class ResultPreview
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
     * @return ResultPreview
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
     * Set votes
     *
     * @param integer $votes
     * @return ResultPreview
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
