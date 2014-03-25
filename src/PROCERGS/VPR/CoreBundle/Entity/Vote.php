<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Vote
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
     * @ORM\Column(name="options", type="text")
     */
    private $options;
    private $plainOptions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="text")
     */
    private $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="authType", type="string", length=255)
     */
    private $authType;

    /**
     * @var string
     *
     * @ORM\Column(name="loginCidadaoId", type="string", length=255)
     */
    private $loginCidadaoId;

    /**
     * @var string
     *
     * @ORM\Column(name="nfgCpf", type="string", length=255)
     */
    private $nfgCpf;

    /**
     * @var string
     *
     * @ORM\Column(name="voterRegistration", type="string", length=12)
     */
    private $voterRegistration;

    /**
     * @ORM\ManyToOne(targetEntity="BallotBox")
     * @ORM\JoinColumn(name="ballot_box_id", referencedColumnName="id", nullable=false)
     */
    protected $ballotBox;

    /**
     * @ORM\ManyToOne(targetEntity="Corede")
     * @ORM\JoinColumn(name="corede_id", referencedColumnName="id")
     */
    protected $corede;

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
     * Set plainOptions
     *
     * @param string $plainOptions
     * @return Vote
     */
    protected function setPlainOptions($plainOptions)
    {
        $this->plainOptions = $plainOptions;

        return $this;
    }

    /**
     * Get plainOptions
     *
     * @return string
     */
    public function getPlainOptions()
    {
        return $this->plainOptions;
    }

    /**
     * Set options
     *
     * @param string $options
     * @return Vote
     */
    protected function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Vote
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set signature
     *
     * @param string $signature
     * @return Vote
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
     * @return Vote
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
     * Set loginCidadaoId
     *
     * @param string $loginCidadaoId
     * @return Vote
     */
    public function setLoginCidadaoId($loginCidadaoId)
    {
        $this->loginCidadaoId = $loginCidadaoId;

        return $this;
    }

    /**
     * Get loginCidadaoId
     *
     * @return string
     */
    public function getLoginCidadaoId()
    {
        return $this->loginCidadaoId;
    }

    /**
     * Set nfgCpf
     *
     * @param string $nfgCpf
     * @return Vote
     */
    public function setNfgCpf($nfgCpf)
    {
        $this->nfgCpf = $nfgCpf;

        return $this;
    }

    /**
     * Get nfgCpf
     *
     * @return string
     */
    public function getNfgCpf()
    {
        return $this->nfgCpf;
    }

    /**
     * Set voterRegistration
     *
     * @param string $voterRegistration
     * @return Vote
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if (!($this->getCreatedAt() instanceof \DateTime)) {
            $this->createdAt = new \DateTime();
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function encryptVote()
    {
        
    }

}
