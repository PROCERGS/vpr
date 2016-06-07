<?php

namespace Donato\OIDCBundle\Event;


use Donato\OIDCBundle\Entity\IdentityProvider;
use Donato\OIDCBundle\Security\Authentication\Token\OIDCToken;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class FilterOIDCTokenEvent extends Event
{
    /** @var OIDCToken */
    protected $token;

    /** @var UserInterface */
    protected $user;

    /** @var IdentityProvider */
    protected $provider;

    /** @var \OpenIDConnectClient */
    protected $oidc;

    public function __construct(
        OIDCToken $token,
        UserInterface $user,
        IdentityProvider $provider,
        \OpenIDConnectClient $oidc
    ) {
        $this->token = $token;
        $this->user = $user;
        $this->provider = $provider;
        $this->oidc = $oidc;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return FilterOIDCTokenEvent
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return IdentityProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param IdentityProvider $provider
     * @return FilterOIDCTokenEvent
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return \OpenIDConnectClient
     */
    public function getOidc()
    {
        return $this->oidc;
    }

    /**
     * @param \OpenIDConnectClient $oidc
     * @return FilterOIDCTokenEvent
     */
    public function setOidc($oidc)
    {
        $this->oidc = $oidc;

        return $this;
    }

    /**
     * @return OIDCToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param OIDCToken $token
     * @return FilterOIDCTokenEvent
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

}