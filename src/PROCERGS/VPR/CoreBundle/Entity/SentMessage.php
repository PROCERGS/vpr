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
     * @ORM\Column(name="destination", type="string", length=255)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_code", type="string", length=255)
     */
    private $smsCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_date", type="datetime")
     */
    private $sentDate;


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
}
