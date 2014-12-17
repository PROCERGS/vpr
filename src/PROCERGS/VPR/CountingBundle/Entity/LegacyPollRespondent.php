<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollRespondent
 *
 * @ORM\Table(name="legacy_poll_respondent", indexes={@ORM\Index(name="fk_poll_respondent_cidadao", columns={"cidadao_id"}), @ORM\Index(name="fk_poll_respondent_poll", columns={"poll_id"})})
 * @ORM\Entity
 */
class LegacyPollRespondent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="poll_id", type="integer", nullable=false)
     */
    private $pollId;

    /**
     * @var integer
     *
     * @ORM\Column(name="cidadao_id", type="integer", nullable=false)
     */
    private $cidadaoId;



    /**
     * Set pollId
     *
     * @param integer $pollId
     * @return PollRespondent
     */
    public function setPollId($pollId)
    {
        $this->pollId = $pollId;

        return $this;
    }

    /**
     * Get pollId
     *
     * @return integer 
     */
    public function getPollId()
    {
        return $this->pollId;
    }

    /**
     * Set cidadaoId
     *
     * @param integer $cidadaoId
     * @return PollRespondent
     */
    public function setCidadaoId($cidadaoId)
    {
        $this->cidadaoId = $cidadaoId;

        return $this;
    }

    /**
     * Get cidadaoId
     *
     * @return integer 
     */
    public function getCidadaoId()
    {
        return $this->cidadaoId;
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
}
