<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * BallotBox
 *
 * @ORM\Table(name="ballot_box")
 * @ORM\Entity
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
     * @Groups({"vote"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"vote"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=255)
     */
    private $secret;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="text")
     */
    private $publicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="text")
     */
    private $privateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Groups({"vote"})
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255, nullable=true)
     * @Groups({"vote"})
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255, nullable=true)
     * @Groups({"vote"})
     */
    private $longitude;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="opening_time", type="datetime", nullable=true)
     * @Groups({"vote"})
     */
    private $openingTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closing_time", type="datetime", nullable=true)
     * @Groups({"vote"})
     */
    private $closingTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_invalid_votes", type="integer")
     */
    private $totalInvalidVotes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_online", type="boolean")
     * @Groups({"vote"})
     */
    private $isOnline;

    /**
     * @ORM\ManyToOne(targetEntity="Poll", inversedBy="ballotBoxes")
     * @ORM\JoinColumn(name="poll_id", referencedColumnName="id", nullable=false)
     * @Groups({"vote"})
     */
    protected $poll;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="ballotBoxes")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     * @Groups({"vote"})
     */
    protected $city;

    public function __construct()
    {
        $this->setTotalInvalidVotes(0);
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

    /**
     * @ORM\PrePersist
     */
    public function setKeys()
    {
        if (!is_null($this->getPrivateKey())) {
            return;
        }

        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $res = openssl_pkey_new($config);

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
    
    public function sign($serializedOptions)
    {
        $encryptedPrivate = $this->getPrivateKey();
        $passphrase = $this->getSecret();
        $privateKey = openssl_pkey_get_private($encryptedPrivate, $passphrase);

        $signature = false;
        openssl_sign($serializedOptions, $signature, $privateKey);

        return base64_encode($signature);
    }

}
