<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Donato\OIDCBundle\Entity\IdentityProvider;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package PROCERGS\VPR\CoreBundle\Entity
 *
 * @ORM\Table(name="oidc_user")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, \Serializable
{
    public static $BASE_ROLES = ['ROLE_USER', 'ROLE_OIDC_USER'];

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Username is the combination of IdP ID and sub. Example:
     * 1#dsgu0s92oijefhkbmn1v35fsdgjgs5465eqtr46dse
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=false, nullable=true)
     */
    protected $username;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_code", type="string", length=255, unique=true, nullable=true)
     */
    protected $confirmationCode;

    /**
     * @var IdentityProvider
     *
     * @ORM\ManyToOne(targetEntity="Donato\OIDCBundle\Entity\IdentityProvider")
     * @ORM\JoinColumn(name="identity_provider_id", referencedColumnName="id")
     */
    protected $identityProvider;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @param string $username
     */
    public function __construct($username = null)
    {
        $this->username = $username;
        $this->roles = self::$BASE_ROLES;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array|null $roles
     */
    public function setRoles(array $roles = null)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     * @return UserInterface
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function equals(UserInterface $user)
    {
        return $user->getUsername() === $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * @param mixed $role
     * @return $this
     */
    public function addRole($role)
    {
        if (!is_array($role)) {
            $role = [$role];
        }

        $this->roles = array_merge($this->roles, $role);

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return IdentityProvider
     */
    public function getIdentityProvider()
    {
        return $this->identityProvider;
    }

    /**
     * @param IdentityProvider $identityProvider
     * @return User
     */
    public function setIdentityProvider($identityProvider)
    {
        $this->identityProvider = $identityProvider;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }

    /**
     * @param string $confirmationCode
     * @return User
     */
    public function setConfirmationCode($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;

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
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->username,
                $this->roles,
            ]
        );
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->username, $this->roles) = unserialize($serialized);
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
