<?php

namespace Donato\OIDCBundle\Security\Authentication\Token;

use Donato\OIDCBundle\Entity\IdentityProvider;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleInterface;

class OIDCToken extends AbstractToken
{
    /** @var string */
    private $accessToken;

    /** @var IdentityProvider */
    private $identityProvider;

    private $roles = array();

    /**
     * OIDCToken constructor.
     * @param string $accessToken
     */
    public function __construct($accessToken, array $roles = array())
    {
        parent::__construct($roles);

        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->accessToken;
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
     */
    public function setIdentityProvider($identityProvider)
    {
        $this->identityProvider = $identityProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->accessToken,
                $this->identityProvider,
                parent::serialize(),
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        list(
            $this->accessToken,
            $this->identityProvider,
            $parent,
            ) = $data;

        parent::unserialize($parent);
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getRoles()
    {
        $roles = $this->getUser()->getRoles();
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = new Role($role);
            } elseif (!$role instanceof RoleInterface) {
                throw new \InvalidArgumentException(sprintf('$roles must be an array of strings, or RoleInterface instances, but got %s.', gettype($role)));
            }

            $this->roles[] = $role;
        }
        return $this->roles;
    }
}
