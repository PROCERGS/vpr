<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Corede
 *
 * @ORM\Table(name="corede")
 * @ORM\Entity
 */
class Corede
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"vote"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"vote"})
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="corede")
     */
    protected $cities;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=7, nullable=true) 
     */
    protected $latitude;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=7, nullable=true)
     */
    protected $longitude;
    
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
     * @return Corede
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
    
    public function setLatitude($var)
    {
        $this->latitude = $var;
    
        return $this;
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }    

    public function setLongitude($var)
    {
        $this->longitude = $var;
    
        return $this;
    }
    
    public function getLongitude()
    {
        return $this->longitude;
    }
    
}
