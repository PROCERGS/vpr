<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

/**
 * Person
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Person extends BaseUser implements OAuthAwareUserProviderInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="Surname", type="string", length=255)
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="voterRegistration", type="string", length=12, nullable=true, unique=true)
     */
    protected $voterRegistration;

    /**
     * @var string
     *
     * @ORM\Column(name="loginCidadaoId", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoId;

    /**
     * @var string
     *
     * @ORM\Column(name="loginCidadaoAccessToken", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="loginCidadaoUsername", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoUsername;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

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
     * Set firstName
     *
     * @param string $firstName
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Person
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set voterRegistration
     *
     * @param string $voterRegistration
     * @return Person
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
     * Set loginCidadaoId
     *
     * @param string $loginCidadaoId
     * @return Person
     */
    public function setLoginCidadaoId($loginCidadaoId)
    {
        $this->loginCidadaoId = $loginCidadaoId;

        return $this;
    }

    /**
     * Get loginCidadaoId
     *
     * @return string
     */
    public function getLoginCidadaoId()
    {
        return $this->loginCidadaoId;
    }

    /**
     * Set loginCidadaoAccessToken
     *
     * @param string $loginCidadaoAccessToken
     * @return Person
     */
    public function setLoginCidadaoAccessToken($loginCidadaoAccessToken)
    {
        $this->loginCidadaoAccessToken = $loginCidadaoAccessToken;

        return $this;
    }

    /**
     * Get loginCidadaoAccessToken
     *
     * @return string
     */
    public function getLoginCidadaoAccessToken()
    {
        return $this->loginCidadaoAccessToken;
    }

    /**
     * Set loginCidadaoUsername
     *
     * @param string $loginCidadaoUsername
     * @return Person
     */
    public function setLoginCidadaoUsername($loginCidadaoUsername)
    {
        $this->loginCidadaoUsername = $loginCidadaoUsername;

        return $this;
    }

    /**
     * Get loginCidadaoUsername
     *
     * @return string
     */
    public function getLoginCidadaoUsername()
    {
        return $this->loginCidadaoUsername;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Person
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if (!($this->getCreatedAt() instanceof \DateTime)) {
            $this->createdAt = new \DateTime();
        }
    }

}
