<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Donato\OIDCBundle\Entity\IdentityProvider;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package PROCERGS\VPR\CoreBundle\Entity
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="PROCERGS\VPR\CoreBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{

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
     * @ORM\Column(type="string", length=255, unique=false)
     */
    protected $username;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $roles;

    /**
     * @param string $username
     */
    public function __construct($username)
    {
        $this->username = $username;
        $this->roles = ['ROLE_USER', 'ROLE_OIDC_USER'];
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->roles;
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
}
