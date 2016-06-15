<?php

namespace PROCERGS\VPR\CoreBundle\Entity\Sms;

use Doctrine\ORM\Mapping as ORM;
use PROCERGS\VPR\CoreBundle\Entity\PollOptionRepository;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;

/**
 * SmsVote
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\Sms\SmsVoteRepository")
 */
class SmsVote
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
     * @ORM\Column(name="sender", type="string", length=255)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="receivedAt", type="datetime")
     */
    private $receivedAt;


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
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set sender
     *
     * @param string $sender
     * @return SmsVote
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return SmsVote
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get receivedAt
     *
     * @return \DateTime
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    /**
     * Set receivedAt
     *
     * @param \DateTime $receivedAt
     * @return SmsVote
     */
    public function setReceivedAt($receivedAt)
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }
}
