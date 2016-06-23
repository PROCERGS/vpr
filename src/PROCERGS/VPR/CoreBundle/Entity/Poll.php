<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Poll
 *
 * @ORM\Table(name="poll")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\PollRepository")
* @UniqueEntity(
 *     fields={"transferYear"},
 *     message="ja existe uma votacao sincronizando com o mesmo ano da Data de fechamento"
 * )
 */
class Poll
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
     * @var \DateTime
     *
     * @ORM\Column(name="opening_time", type="datetime", nullable=false)
     * @Groups({"vote", "setup"})
     */
    protected $openingTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closing_time", type="datetime", nullable=false)
     * @Groups({"vote", "setup"})
     */
    protected $closingTime;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Groups({"vote", "setup"})
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="text")
     * @Groups("setup")
     */
    protected $publicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="text")
     */
    protected $privateKey;

    /**
     * @ORM\Column(name="secret", type="string", length=255)
     * @var string
     */
    protected $secret;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="apuration_time", type="datetime", nullable=false)     
     */
    protected $apurationTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="apuration_done", type="datetime", nullable=true)     
     */
    protected $apurationDone;

    /**
     * @ORM\OneToMany(targetEntity="BallotBox", mappedBy="poll")
     */
    protected $ballotBoxes;

    /**
     * @ORM\OneToMany(targetEntity="Step", mappedBy="poll")
     * @ORM\OrderBy({"sorting" = "asc"})
     * @Groups("setup")
     */
    protected $steps;

    protected $blocked;
    
    /**
     * @ORM\Column(name="transfer_pool_option_status", type="integer", nullable=false)     
     */
    protected $transferPoolOptionStatus = 0;
    
    /**
     * @ORM\Column(name="transfer_open_vote_status", type="integer", nullable=false)
     */
    protected $transferOpenVoteStatus = 0;
    
    /**
     * @ORM\Column(name="apuration_status", type="integer", nullable=false)
     */
    protected $apurationStatus = 0;
    
    /**
     * @ORM\Column(name="transfer_year", type="integer", nullable=true)
     */
    protected $transferYear;
    
    /**
     * @ORM\Column(name="ppp_cod_programa", type="integer", nullable=true)
     */
    protected $pppCodPrograma;
    /**
     * @ORM\Column(name="ppp_cod_projeto", type="integer", nullable=true)
     */
    protected $pppCodProjeto;

    public function __construct()
    {
        //$this->ballotBoxes = new ArrayCollection();
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
     * @return Poll
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
     * Set openingTime
     *
     * @param \DateTime $openingTime
     * @return Poll
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
     * Set closingTimime
     *
     * @param \DateTime $closingTimime
     * @return Poll
     */
    public function setClosingTime($var)
    {
        $this->closingTime = $var;

        return $this;
    }

    /**
     * Get closingTimime
     *
     * @return \DateTime
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Poll
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set publicKey
     *
     * @param string $publicKey
     * @return Poll
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
     * Set apurationTime
     *
     * @param \DateTime $apurationTime
     * @return Poll
     */
    public function setApurationTime($apurationTime)
    {
        $this->apurationTime = $apurationTime;

        return $this;
    }

    /**
     * Get apurationTime
     *
     * @return \DateTime
     */
    public function getApurationTime()
    {
        return $this->apurationTime;
    }

    /**
     * Set apurationDone
     *
     * @param \DateTime $apurationDone
     * @return Poll
     */
    public function setApurationDone($apurationDone)
    {
        $this->apurationDone = $apurationDone;

        return $this;
    }

    /**
     * Get apurationDone
     *
     * @return \DateTime
     */
    public function getApurationDone()
    {
        return $this->apurationDone;
    }

    public function getBallotBoxes()
    {
        return $this->ballotBoxes;
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function setSecret($var)
    {
    	$this->secret = $var;
    }

    public function getSecret()
    {
    	return $this->secret;
    }

    public function setPrivateKey($var)
    {
    	$this->privateKey = $var;
    }

    public function getPrivateKey()
    {
    	return $this->privateKey;
    }

    public function generatePrivateAndPublicKeys()
    {
    	if (null === $this->id) {
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

    		$chars = 'abcdefghjkmnpqrstuvwxyz';
    		$secret = substr(str_shuffle($chars), 0, 10);

	    	openssl_pkey_export($res, $privKey, $secret);

	    	$details = openssl_pkey_get_details($res);
	    	$pubKey  = $details["key"];

	    	$this->setPrivateKey($privKey);
	    	$this->setPublicKey($pubKey);
	    	$this->setSecret($secret);
    	}
    }

    public function isBiggerThen1Day()
    {
    	if ($this->openingTime->format('Y-m-d') != $this->closingTime->format('Y-m-d')) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function setBlocked($var) {
        $this->blocked = $var;
    }

    public function getBlocked() {
        return $this->blocked;
    }
    
    public function setTransferOpenVoteStatus($var) {
        $this->transferOpenVoteStatus = $var;
    }    
    public function getTransferOpenVoteStatus() {
        return $this->transferOpenVoteStatus;
    }
    public function setTransferPoolOptionStatus($var) {
        $this->transferPoolOptionStatus = $var;
    }
    public function getTransferPoolOptionStatus() {
        return $this->transferPoolOptionStatus;
    }
    public function setApurationStatus($var) {
        $this->apurationStatus = $var;
    }
    public function getApurationStatus() {
        return $this->apurationStatus;
    }
    public function setTransferYear($var) {
        $this->transferYear = $var;
    }
    public function getTransferYear() {
        return $this->transferYear;
    }
    public function setPppCodPrograma($var) {
        $this->pppCodPrograma = $var;
    }
    public function getPppCodPrograma() {
        return $this->pppCodPrograma;
    }
    public function setPppCodProjeto($var) {
        $this->pppCodProjeto = $var;
    }
    public function getPppCodProjeto() {
        return $this->pppCodProjeto;
    }
}
