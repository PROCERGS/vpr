<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TREVoter
 *
 * @ORM\Entity
 * @ORM\Table(
 *      indexes={
 *          @ORM\Index(name="index_trevoter_name", columns={"name"}),
 *          @ORM\Index(name="index_trevoter_cityCode", columns={"city_code"}),
 *          @ORM\Index(name="index_trevoter_zone", columns={"votingZone"}),
 *      }
 * )
 */
class TREVoter
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_code", type="integer")
     */
    private $cityCode;

    /**
     * @var string
     *
     * @ORM\Column(name="voterRegistration", type="string", length=12, unique=true)
     */
    private $voterRegistration;

    /**
     * @var string
     *
     * @ORM\Column(name="votingZone", type="string", length=255)
     */
    private $votingZone;

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
     * Set city
     *
     * @param string $city
     * @return TREVoter
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
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
     * Get city
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

}
