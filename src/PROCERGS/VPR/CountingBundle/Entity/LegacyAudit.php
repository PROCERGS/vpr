<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Audit
 *
 * @ORM\Table(name="legacy_audit", indexes={@ORM\Index(name="action", columns={"action"}), @ORM\Index(name="index_table", columns={"table"}), @ORM\Index(name="index_target_id", columns={"target_id"})})
 * @ORM\Entity
 */
class LegacyAudit
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
     * @var string
     *
     * @ORM\Column(name="table", type="string", length=255, nullable=false)
     */
    private $table;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_id", type="integer", nullable=false)
     */
    private $targetId;

    /**
     * @var string
     *
     * @ORM\Column(name="query", type="text", nullable=false)
     */
    private $query;

    /**
     * @var string
     *
     * @ORM\Column(name="values", type="text", nullable=true)
     */
    private $values;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", nullable=false)
     */
    private $action;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=20, nullable=false)
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;



    /**
     * Set table
     *
     * @param string $table
     * @return Audit
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return string 
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set targetId
     *
     * @param integer $targetId
     * @return Audit
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId
     *
     * @return integer 
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set query
     *
     * @param string $query
     * @return Audit
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string 
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set values
     *
     * @param string $values
     * @return Audit
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return string 
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Audit
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Audit
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Audit
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Audit
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
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
