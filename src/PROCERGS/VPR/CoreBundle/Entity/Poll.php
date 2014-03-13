<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Poll
 *
 * @ORM\Table(name="poll")
 * @ORM\Entity
 */
class Poll
{
    /**
     * @var integer
     *
     * @ORM\Column(name="votacao_id", type="integer", nullable=false)
     */
    private $votacaoId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set votacaoId
     *
     * @param integer $votacaoId
     * @return Poll
     */
    public function setVotacaoId($votacaoId)
    {
        $this->votacaoId = $votacaoId;

        return $this;
    }

    /**
     * Get votacaoId
     *
     * @return integer 
     */
    public function getVotacaoId()
    {
        return $this->votacaoId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Poll
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
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
