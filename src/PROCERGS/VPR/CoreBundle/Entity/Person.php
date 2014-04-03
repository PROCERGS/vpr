<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use JMS\Serializer\Annotation\Groups;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email",
 *          column=@ORM\Column(
 *              name     = "email",
 *              type     = "string",
 *              length   = 255,
 *              nullable = true
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="emailCanonical",
 *          column=@ORM\Column(
 *              name     = "email_canonical",
 *              type     = "string",
 *              nullable = true,
 *              unique   = true,
 *              length   = 255
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="password",
 *          column=@ORM\Column(
 *              nullable = true
 *          )
 *      )
 * })
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
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_id", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoId;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_access_token", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="login_cidadao_username", type="string", length=255, nullable=true)
     */
    protected $loginCidadaoUsername;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     * @Groups({"vote"})
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="TREVoter")
     * @ORM\JoinColumn(name="tre_voter_id", referencedColumnName="id", nullable=true, unique=true)
     * @Groups({"vote"})
     */
    protected $treVoter;

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

    public function getCity()
    {
        return $this->city;
    }

    public function setCity(City $city)
    {
        $this->city = $city;

        return $this;
    }

    public function getTreVoter()
    {
        return $this->treVoter;
    }

    public function setTreVoter(TREVoter $var)
    {
        $this->treVoter = $var;

        return $this;
    }

}
