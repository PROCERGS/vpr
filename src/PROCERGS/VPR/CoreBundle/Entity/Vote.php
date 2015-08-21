<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PROCERGS\VPR\CoreBundle\Exception\OpenSSLException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Vote
 *
 * @ORM\Table(name="vote", indexes={
 *      @ORM\Index(name="idx_voter_registration", columns={"voter_registration"}),
 *      @ORM\Index(name="idx_login_cidadao_id", columns={"login_cidadao_id"}),
 *      @ORM\Index(name="idx_nfg_cpf", columns={"nfg_cpf"})
 * }, uniqueConstraints={
 *      @ORM\UniqueConstraint(name="offline_id_unique", columns={"ballot_box_id", "offline_id"})
 * })
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\VoteRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"ballotBox", "offlineId"}, message="message")
 */
class Vote
{
    const AUTH_LOGIN_CIDADAO      = 'lc';
    const AUTH_VOTER_REGISTRATION = 'doc';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"vote"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="text")
     * @JMS\Groups({"vote"})
     */
    protected $options;
    protected $plainOptions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Groups({"vote"})
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="text")
     * @JMS\Groups({"vote"})
     */
    protected $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="passphrase", type="text")
     * @JMS\Groups({"vote"})
     */
    protected $passphrase;

    /**
     * @var string
     *
     * @ORM\Column(name="auth_type", type="string", length=255)
     * @JMS\Groups({"vote"})
     */
    protected $authType;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_id", type="string", length=255, nullable=true)
     * @JMS\Groups({"vote"})
     */
    protected $loginCidadaoId;

    /**
     * @var string
     *
     * @ORM\Column(name="nfg_cpf", type="string", length=255, nullable=true)
     * @JMS\Groups({"vote"})
     */
    protected $nfgCpf;

    /**
     * @var string
     *
     * @ORM\Column(name="voter_registration", type="string", length=12, nullable=true)
     * @JMS\Groups({"vote"})
     */
    protected $voterRegistration;

    /**
     * @ORM\ManyToOne(targetEntity="BallotBox")
     * @ORM\JoinColumn(name="ballot_box_id", referencedColumnName="id", nullable=false)
     * @JMS\Groups({"vote"})
     * @JMS\Type("PROCERGS\VPR\CoreBundle\Entity\BallotBox")
     */
    protected $ballotBox;

    /**
     * @ORM\ManyToOne(targetEntity="Corede")
     * @ORM\JoinColumn(name="corede_id", referencedColumnName="id", nullable=false)
     * @JMS\Groups({"vote"})
     * @JMS\Type("PROCERGS\VPR\CoreBundle\Entity\Corede")
     */
    protected $corede;
    protected $lastStep;
    protected $pollOption = array();

    /**
     * @ORM\Column(name="wb_treatment_vpr", type="integer", nullable=true)
     * @JMS\Groups({"vote"})
     */
    protected $treatmentVPR;

    /**
     * @ORM\Column(name="wb_treatment_gabinete_digital", type="integer", nullable=true)
     * @JMS\Groups({"vote"})
     */
    protected $treatmentGabineteDigital;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     * @JMS\Groups({"vote"})
     * @JMS\Type("PROCERGS\VPR\CoreBundle\Entity\City")
     */
    protected $city;

    /**
     * @ORM\Column(name="ip_address", type="string", length=46, nullable=true)
     * @JMS\Groups({"vote"})
     * @var string
     */
    protected $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="offline_id", type="string", length=255, nullable=true, unique=true)
     * @JMS\Groups({"vote"})
     */
    protected $offlineId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_invalid", type="boolean", nullable=true)
     */
    protected $isInvalid;

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
    public function setPlainOptions($plainOptions)
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
     * Set passphrase
     *
     * @param string $passphrase
     * @return Vote
     */
    public function setPassphrase($passphrase)
    {
        $this->passphrase = $passphrase;

        return $this;
    }

    /**
     * Get passphrase
     *
     * @return string
     */
    public function getPassphrase()
    {
        return $this->passphrase;
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
     *
     * @return BallotBox
     */
    public function getBallotBox()
    {
        return $this->ballotBox;
    }

    /**
     *
     * @param BallotBox $ballotBox
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function setBallotBox($ballotBox)
    {
        $this->ballotBox = $ballotBox;

        return $this;
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

    public function encryptVote()
    {
        $crypted   = null;
        $ballotBox = $this->getBallotBox();
        if (!$ballotBox) {
            return false;
        }
        $poll          = $ballotBox->getPoll();
        $pollPublicKey = openssl_get_publickey($poll->getPublicKey());
        $options       = $this->getPlainOptions();

        $passphrases = null;
        if (openssl_seal($options, $crypted, $passphrases, array($pollPublicKey))
            !== false) {
            $this->setOptions(base64_encode($crypted));
            $passphrase = base64_encode(reset($passphrases));
            $this->setPassphrase($passphrase);
        } else {
            throw new OpenSSLException();
        }
    }

    public function openVote($privateKey)
    {
        $openVote   = null;
        $options    = base64_decode($this->getOptions());
        $passphrase = base64_decode($this->getPassphrase());
        if (openssl_open($options, $openVote, $passphrase, $privateKey) === false) {
            $message = openssl_error_string();
            throw new OpenSSLException($message);
        }
        $this->setPlainOptions($openVote);

        return $this;
    }

    /**
     * @param type $var
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function setCorede($var)
    {
        $this->corede = $var;

        return $this;
    }

    public function getCorede()
    {
        return $this->corede;
    }

    public function setLastStep($var)
    {
        $this->lastStep = $var;

        return $this;
    }

    public function getLastStep()
    {
        return $this->lastStep;
    }

    public function addPollOption($var)
    {
        if (!$this->pollOption) {
            $this->pollOption = $var;
        } else {
            if (!$var) {
                $var = array();
            }
            $this->pollOption = array_merge($this->pollOption, $var);
        }
        return $this;
    }

    public function setPollOption($var)
    {
        $this->pollOption = $var;
        return $this;
    }

    public function getPollOption()
    {
        return $this->pollOption;
    }

    public function close($passphrase)
    {
        if (!$this->plainOptions) {
            return false;
        }
        $this->encryptVote();
        $this->signature = $this->ballotBox->sign(
            $this->getOptions(), $passphrase
        );
    }

    public function getTreatmentVPR()
    {
        return $this->treatmentVPR;
    }

    public function setTreatmentVPR($treatmentVPR)
    {
        $this->treatmentVPR = $treatmentVPR;

        return $this;
    }

    public function getTreatmentGabineteDigital()
    {
        return $this->treatmentGabineteDigital;
    }

    public function setTreatmentGabineteDigital($treatmentGabineteDigital)
    {
        $this->treatmentGabineteDigital = $treatmentGabineteDigital;

        return $this;
    }

    /**
     * @param type $var
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function setCity($var)
    {
        $this->city = $var;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param type $ipAddress
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getOfflineId()
    {
        return $this->offlineId;
    }

    /**
     * @param type $offlineId
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function setOfflineId($offlineId)
    {
        $this->offlineId = $offlineId;
        return $this;
    }

    /**
     *
     * @param type $vote
     * @param \PROCERGS\VPR\CoreBundle\Entity\BallotBox $ballotBox
     * @param \PROCERGS\VPR\CoreBundle\Entity\Corede $corede
     * @param \PROCERGS\VPR\CoreBundle\Entity\City $city
     * @param string $serializedOptions
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    public function populateOfflineVote($vote, BallotBox $ballotBox,
                                        Corede $corede, City $city,
                                        $serializedOptions)
    {
        $this->setOfflineId($vote['id'])
            ->setCreatedAt($vote['createdAt'])
            ->setAuthType($vote['authType'])
            ->setBallotBox($ballotBox)
            ->setCorede($corede)
            ->setCity($city)
            ->setVoterRegistration($vote['voterRegistration'])
            ->setPlainOptions($serializedOptions);

        $this->close($ballotBox->getSecret());

        return $this;
    }
}
