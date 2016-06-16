<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BallotBox
 *
 * @ORM\Table(name="ballot_box")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\BallotBoxRepository")
 * @UniqueEntity({"pin"})
 * @ORM\HasLifecycleCallbacks
 */
class BallotBox
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"vote", "setup"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"vote", "setup"})
     */
    protected $name;

    /**
     * @ORM\Column(name="secret", type="string", length=255)
     * @var string
     */
    protected $secret;

    /**
     * @var integer
     *
     * @Assert\Range(min = 0, max = 999999)
     * @ORM\Column(name="pin", type="integer", nullable=false)
     */
    protected $pin;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="text")
     * @Groups({"setup"})
     */
    protected $publicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="text")
     * @Groups({"setup"})
     */
    protected $privateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Groups({"vote", "setup"})
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255, nullable=true)
     * @Groups({"vote"})
     */
    protected $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255, nullable=true)
     * @Groups({"vote"})
     */
    protected $longitude;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="opening_time", type="datetime", nullable=true)
     * @Groups({"vote", "setup"})
     */
    protected $openingTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closing_time", type="datetime", nullable=true)
     * @Groups({"vote", "setup"})
     */
    protected $closingTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_invalid_votes", type="integer")
     */
    protected $totalInvalidVotes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_online", type="boolean")
     * @Groups({"vote"})
     */
    protected $isOnline;

    /**
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="ballotBoxes")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id", nullable=false)
     * @Groups({"vote", "setup"})
     */
    protected $poll;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="ballotBoxes")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     * @Groups({"vote", "setup"})
     */
    protected $city;

    /**
     * @ORM\Column(name="setup_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $setupAt;

    /**
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $closedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="csv", type="text", nullable=true)
     */
    protected $csv;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="fone", type="string", length=9, nullable=true)
     */
    protected $fone;
    /**
     * @var string
     *
     * @ORM\Column(name="ddd", type="string", length=2, nullable=true)
     */
    protected $ddd;

    /**
     * @var string
     *
     * @ORM\Column(name="sent_message1_id", type="integer", nullable=true)
     */
    protected $sentMessage1Id;
    /**
     * @var string
     *
     * @ORM\Column(name="sent_message2_id", type="integer", nullable=true)
     */
    protected $sentMessage2Id;

    /**
     * @ORM\ManyToOne(targetEntity="SentMessage")
     * @ORM\JoinColumn(name="sent_message1_id", referencedColumnName="id", nullable=true)
     */
    protected $sentMessage1;
    /**
     * @ORM\ManyToOne(targetEntity="SentMessage")
     * @ORM\JoinColumn(name="sent_message2_id", referencedColumnName="id", nullable=true)
     */
    protected $sentMessage2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sms", type="boolean")
     * @Groups({"vote"})
     */
    protected $isSms;

    public function __construct()
    {
        $this->setTotalInvalidVotes(0);
        $this->isSms = false;
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

    public function setId($var)
    {
        $this->id = $var;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BallotBox
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set secret
     *
     * @param string $secret
     * @return BallotBox
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set publicKey
     *
     * @param string $publicKey
     * @return BallotBox
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get publicKey
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Set privateKey
     *
     * @param string $privateKey
     * @return BallotBox
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Get privateKey
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return BallotBox
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return BallotBox
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return BallotBox
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set openingTime
     *
     * @param \DateTime $openingTime
     * @return BallotBox
     */
    public function setOpeningTime($openingTime)
    {
        $this->openingTime = $openingTime;

        return $this;
    }

    /**
     * Get openingTime
     *
     * @return \DateTime
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }

    /**
     * Set closingTime
     *
     * @param \DateTime $closingTime
     * @return BallotBox
     */
    public function setClosingTime($closingTime)
    {
        $this->closingTime = $closingTime;

        return $this;
    }

    /**
     * Get closingTime
     *
     * @return \DateTime
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * Set totalInvalidVotes
     *
     * @param integer $totalInvalidVotes
     * @return BallotBox
     */
    public function setTotalInvalidVotes($totalInvalidVotes)
    {
        $this->totalInvalidVotes = $totalInvalidVotes;

        return $this;
    }

    /**
     * Get totalInvalidVotes
     *
     * @return integer
     */
    public function getTotalInvalidVotes()
    {
        return $this->totalInvalidVotes;
    }

    /**
     * Set isOnline
     *
     * @param boolean $isOnline
     * @return BallotBox
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    /**
     * Get isOnline
     *
     * @return boolean
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    public function setKeys()
    {
        if (!is_null($this->getPrivateKey())) {
            return;
        }

        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            "encrypt_key" => true,
        );

        $res = openssl_pkey_new($config);
        if (!$res) {
            $a = openssl_error_string();
        }

        openssl_pkey_export($res, $privKey, $this->getSecret());

        $details = openssl_pkey_get_details($res);
        $pubKey = $details["key"];

        $this->setPrivateKey($privKey);
        $this->setPublicKey($pubKey);
    }

    /**
     * @param \PROCERGS\VPR\CoreBundle\Entity\Poll $poll
     * @return \PROCERGS\VPR\CoreBundle\Entity\BallotBox
     */
    public function setPoll(Poll $poll)
    {
        $this->poll = $poll;

        return $this;
    }

    /**
     * @return Poll
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * @param \PROCERGS\VPR\CoreBundle\Entity\City $city
     * @return \PROCERGS\VPR\CoreBundle\Entity\City
     */
    public function setCity(\PROCERGS\VPR\CoreBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    public function sign($serializedOptions, $passphrase)
    {
        $encryptedPrivate = $this->getPrivateKey();
        $privateKey = openssl_pkey_get_private(
            $encryptedPrivate,
            $passphrase
        );

        $signature = false;
        openssl_sign($serializedOptions, $signature, $privateKey);

        return base64_encode($signature);
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    public function generatePassphrase($length = 10)
    {
        $chars = 'abcdefghjkmnpqrstuvwxyz';

        return substr(str_shuffle($chars), 0, $length);
    }

    public function getSetupAt()
    {
        return $this->setupAt;
    }

    public function getClosedAt()
    {
        return $this->closedAt;
    }

    public function setSetupAt(\DateTime $setupAt)
    {
        $this->setupAt = $setupAt;

        return $this;
    }

    public function setClosedAt(\DateTime $closedAt)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getCsv()
    {
        return $this->csv;
    }

    public function setCsv($var)
    {
        $this->csv = $var;

        return $this;
    }

    public function getFone()
    {
        return $this->fone;
    }

    public function setFone($var)
    {
        $this->fone = $var;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($var)
    {
        $this->email = $var;

        return $this;
    }

    public function getDdd()
    {
        return $this->ddd;
    }

    public function setDdd($var)
    {
        $this->ddd = $var;

        return $this;
    }

    public function getSentMessage1Id()
    {
        return $this->sentMessage1Id;
    }

    public function setSentMessage1Id($var)
    {
        $this->sentMessage1Id = $var;
    }

    public function getSentMessage2Id()
    {
        return $this->sentMessage2Id;
    }

    public function setSentMessage2Id($var)
    {
        $this->sentMessage2Id = $var;
    }

    public function getSentMessage1()
    {
        return $this->sentMessage1;
    }

    public function setSentMessage1($var)
    {
        $this->sentMessage1 = $var;
    }

    public function getSentMessage2()
    {
        return $this->sentMessage2;
    }

    public function setSentMessage2($var)
    {
        $this->sentMessage2 = $var;
    }

    protected static $allowedStatus1 = array(1 => 'Disponível', 2 => 'Ativa', 3 => 'Encerrada');

    public static function getAllowedStatus1($var = null)
    {
        if (null === $var) {
            return self::$allowedStatus1;
        } else {
            return self::$allowedStatus1[$var];
        }
    }

    public function getStatus1Label()
    {
        if (null === $this->setupAt) {
            return self::$allowedStatus1[1];
        } else {
            if (null === $this->closedAt) {
                return self::$allowedStatus1[2];
            } else {
                return self::$allowedStatus1[3];
            }
        }
    }

    /**
     * @return boolean
     */
    public function isSms()
    {
        return $this->isSms;
    }

    /**
     * @param boolean $isSms
     * @return BallotBox
     */
    public function setIsSms($isSms)
    {
        $this->isSms = $isSms;

        return $this;
    }
}
