<?php

namespace PROCERGS\VPR\CoreBundle\Entity;

use Donato\OIDCBundle\Entity\IdentityProviderManager;
use Donato\OIDCBundle\Security\Authentication\Token\OIDCToken;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserManager
{
    /** @var IdentityProviderManager */
    protected $idpManager;

    /** @var TokenStorage */
    protected $tokenStorage;

    /** @var Session */
    protected $session;

    public function __construct(IdentityProviderManager $idpManager, TokenStorage $tokenStorage, Session $session)
    {
        $this->idpManager = $idpManager;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * @return boolean
     */
    public function isEmailVerified()
    {
        $emailVerified = $this->session->get('email_verified', null);

        if ($emailVerified !== null) {
            return $emailVerified;
        }
        $oidc = $this->getOpenIDConnectClient();
        $emailVerified = $oidc->requestUserInfo('email_verified');
        $this->session->set('email_verified', $emailVerified);

        return $emailVerified;
    }

    /**
     * @return \OpenIDConnectClient
     */
    private function getOpenIDConnectClient()
    {
        /** @var OIDCToken $token */
        $token = $this->tokenStorage->getToken();
        $accessToken = $token->getAccessToken();
        $oidc = $this->idpManager->getOpenIDConnectClient($token->getIdentityProvider()->getProviderUrl());
        $oidc->setAccessToken($accessToken);

        return $oidc;
    }

    public function checkEmailVerified(Event $event, RouterInterface $router)
    {
        $emailVerified = $this->isEmailVerified();

        if ($emailVerified) {
            return true;
        }

        /** @var OIDCToken $token */
        $token = $this->tokenStorage->getToken();
        $provider = $token->getIdentityProvider();
        $scope = ['email'];

        $parsedUrl = parse_url($provider->getProviderUrl());
        $parsedUrl['path'] = sprintf("/client/%s/dynamic-form", $provider->getClientId());
        $parsedUrl['query'] = http_build_query(
            [
                'scope' => implode(' ', $scope),
                'redirect_url' => $router->generate('oidc_check_email', [], RouterInterface::ABSOLUTE_URL),
            ]
        );

        $url = htmlentities($this->unparse_url($parsedUrl));
        $callback = $router->generate('oidc_redirect', compact('url'), RouterInterface::ABSOLUTE_URL);
        $event->setResponse(new RedirectResponse($callback));
        $event->stopPropagation();

        return false;
    }

    private function unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'].'://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':'.$parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':'.$parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?'.$parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#'.$parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
