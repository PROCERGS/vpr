<?php

namespace Donato\OIDCBundle\Security\Authentication\Token;

use Donato\OIDCBundle\Entity\IdentityProvider;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OIDCToken extends AbstractToken
{
    /** @var string */
    private $accessToken;

    /** @var IdentityProvider */
    private $identityProvider;

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
}
