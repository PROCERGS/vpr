<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollRespondent
 *
 * @ORM\Table(name="poll_respondent", indexes={@ORM\Index(name="fk_poll_respondent_cidadao", columns={"cidadao_id"}), @ORM\Index(name="fk_poll_respondent_poll", columns={"poll_id"})})
 * @ORM\Entity
 */
class PollRespondent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Poll
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Poll")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poll_id", referencedColumnName="id")
     * })
     */
    private $poll;

    /**
     * @var \PROCERGS\VPR\CoreBundle\Entity\Cidadao
     *
     * @ORM\ManyToOne(targetEntity="PROCERGS\VPR\CoreBundle\Entity\Cidadao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cidadao_id", referencedColumnName="id_cidadao")
     * })
     */
    private $cidadao;



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
     * Set poll
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Poll $poll
     * @return PollRespondent
     */
    public function setPoll(\PROCERGS\VPR\CoreBundle\Entity\Poll $poll = null)
    {
        $this->poll = $poll;

        return $this;
    }

    /**
     * Get poll
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Poll 
     */
    public function getPoll()
    {
        return $this->poll;
    }

    /**
     * Set cidadao
     *
     * @param \PROCERGS\VPR\CoreBundle\Entity\Cidadao $cidadao
     * @return PollRespondent
     */
    public function setCidadao(\PROCERGS\VPR\CoreBundle\Entity\Cidadao $cidadao = null)
    {
        $this->cidadao = $cidadao;

        return $this;
    }

    /**
     * Get cidadao
     *
     * @return \PROCERGS\VPR\CoreBundle\Entity\Cidadao 
     */
    public function getCidadao()
    {
        return $this->cidadao;
    }
}
