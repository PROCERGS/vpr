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
 *          @ORM\Index(name="index_trevoter_cityCode", columns={"city_code"}),
 *          @ORM\Index(name="index_trevoter_zone", columns={"voting_zone"}),
 *      }
 * )
 */
class TREVoter
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="city_name", type="string", length=255)
     */
    private $cityName;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_code", type="integer")
     */
    private $cityCode;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="voter_registration", type="string", length=12, unique=true)
     */
    private $voterRegistration;

    /**
     * @var string
     *
     * @ORM\Column(name="voting_zone", type="string", length=255)
     */
    private $votingZone;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_code", referencedColumnName="id", nullable=true)
     */
    protected $city;

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
     * Set voterRegistration
     *
     * @param string $voterRegistration
     * @return TREVoter
     */
    public function setVoterRegistration($voterRegistration)
    {
        $this->voterRegistration = $voterRegistration;

        return $this;
    }

    /**
     * Get voterRegistration
     *
     * @return string
     */
    public function getVoterRegistration()
    {
        return $this->voterRegistration;
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
