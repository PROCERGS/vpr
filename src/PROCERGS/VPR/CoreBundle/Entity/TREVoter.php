<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TREVoter
 *
 * @ORM\Entity
 * @ORM\Table(name="tre_voter",
 *      indexes={
 *          @ORM\Index(name="index_trevoter_name", columns={"name"}),
 *          @ORM\Index(name="index_trevoter_zone", columns={"voting_zone"}),
 *      }
 * )
 */
class TREVoter
{

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=12)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="city_name", type="string", length=255)
     */
    protected $cityName;

    /**
     * @var string
     *
     * @ORM\Column(name="voting_zone", type="string", length=255)
     */
    protected $votingZone;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     */
    protected $city;
    
    /**
     * @ORM\ManyToOne(targetEntity="Corede")
     * @ORM\JoinColumn(name="corede_id", referencedColumnName="id", nullable=true)
     */
    protected $corede;    

    
    public function __construct(&$id = null)
    {
        $this->setId($id);
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return TREVoter
     */
    public function setId($var)
    {
        $this->id = $var;
    
        return $this;
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

    /**
     * Set name
     *
     * @param string $name
     * @return TREVoter
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
     * Set cityName
     *
     * @param string $cityName
     * @return TREVoter
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Get cityName
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * Set cityCode
     *
     * @param string $cityCode
     * @return TREVoter
     */
    public function setCityCode($cityCode)
    {
        $this->cityCode = $cityCode;

        return $this;
    }

    /**
     * Get cityCode
     *
     * @return string
     */
    public function getCityCode()
    {
        return $this->cityCode;
    }

    /**
     * Set votingZone
     *
     * @param string $votingZone
     * @return TREVoter
     */
    public function setVotingZone($votingZone)
    {
        $this->votingZone = $votingZone;

        return $this;
    }

    /**
     * Get votingZone
     *
     * @return string
     */
    public function getVotingZone()
    {
        return $this->votingZone;
    }

    /**
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

}
