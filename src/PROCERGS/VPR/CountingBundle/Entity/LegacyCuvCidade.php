<?php

namespace PROCERGS\VPR\CountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuvCidade
 *
 * @ORM\Table(name="legacy_cuv_cidade")
 * @ORM\Entity
 */
class LegacyCuvCidade
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
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;



    /**
     * Set name
     *
     * @param string $name
     * @return CuvCidade
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
