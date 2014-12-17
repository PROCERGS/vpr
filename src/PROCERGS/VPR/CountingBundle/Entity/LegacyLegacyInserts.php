<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LegacyInserts
 *
 * @ORM\Table(name="legacy_legacy_inserts")
 * @ORM\Entity
 */
class LegacyLegacyInserts
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
     * @ORM\Column(name="query", type="string", length=155, nullable=true)
     */
    private $query;



    /**
     * Set query
     *
     * @param string $query
     * @return LegacyInserts
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
