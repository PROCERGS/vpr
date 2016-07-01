<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SentMessages
 *
 * @ORM\Table(name="sent_message")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\SentMessageRepository")
 */
class SentMessage
{
    const MODE_EMAIL = 1;
    const MODE_SMS = 2;
    const TYPE_SENHA = 1;
    const TYPE_REQUISICAO = 2;
    const TYPE_GENERICA = 3;
    
    /**
     * @ORM\ManyToOne(targetEntity="BallotBox")
     * @ORM\JoinColumn(name="ballot_box_id", referencedColumnName="id", nullable=false)
     */
    protected $ballotBox;
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
     * @ORM\Column(name="destination", type="string", length=255, nullable=false)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_code", type="string", length=255, nullable=true)
     */
    private $smsCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_date", type="datetime", nullable=false)
     */
    private $sentDate;
    
    /**
     * @ORM\Column(name="sent_message_type_id", type="integer", nullable=false)
     */
    private $sentMessageTypeId;
    
    /**
     * @ORM\Column(name="sent_message_mode_id", type="integer", nullable=false)
     */
    private $sentMessageModeId;
    
    /**
     * @ORM\Column(name="success", type="boolean", nullable=false)
     */
    private $success;
    
    protected $ballotBoxId;

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
     * Get destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set destination
     *
     * @param string $destination
     * @return SentMessage
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get smsCode
     *
     * @return string
     */
    public function getSmsCode()
    {
        return $this->smsCode;
    }

    /**
     * Set smsCode
     *
     * @param string $smsCode
     * @return SentMessage
     */
    public function setSmsCode($smsCode)
    {
        $this->smsCode = $smsCode;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set date
     *
     * @param \DateTime $sentDate
     * @return SentMessage
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBallotBox()
    {
        return $this->ballotBox;
    }

    /**
     * @param mixed $ballotBox
     * @return SentMessage
     */
    public function setBallotBox($ballotBox)
    {
        $this->ballotBox = $ballotBox;

        return $this;
    }
    
    public function setId($var)
    {
        $this->id = $var;
    }
    public function setSuccess($var)
    {
        $this->success = $var;
    }
    public function getSuccess()
    {
        return $this->success;
    }
    public function setBallotBoxId($var)
    {
        $this->ballotBoxId = $var;
    }
    public function getBallotBoxId()
    {
        return $this->ballotBoxId;
    }
    public function setSentMessageTypeId($var)
    {
        $this->sentMessageTypeId = $var;
    }
    public function getSentMessageTypeId()
    {
        return $this->sentMessageTypeId;
    }
    
    public function setSentMessageModeId($var)
    {
        $this->sentMessageModeId = $var;
    }
    public function getSentMessageModeId()
    {
        return $this->sentMessageModeId;
    }
    
    public function getSentDateToDb()
    {
        if (null !== $this->sentDate && $this->sentDate instanceof \DateTime) {
            return $this->sentDate->format('Y-m-d H:i:s');
        }
        return null;
    }
        
    public function getSuccessToDb()
    {
        if (null !== $this->success) {
            if ($this->success) {
                return 1;
            } else {
                return 0;
            }
        }
        return null;
    }
    
    public function getDestinationToDb()
    {
        if (null === $this->destination) {
            return null;
        }
        $t = trim($this->destination);
        return strlen($t) ? $t : null;
    }
    
    public function getSmsCodeToDb()
    {
        if (null === $this->smsCode) {
            return null;
        }
        $t = trim($this->smsCode);
        return strlen($t) ? $t : null;
    }
    
}
 