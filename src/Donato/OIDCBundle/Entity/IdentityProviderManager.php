<?php

namespace Donato\OIDCBundle\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Donato\OIDCBundle\Repository\IdentityProviderRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IdentityProviderManager
{
    /** @var EntityManager */
    private $em;

    /** @var IdentityProviderRepository */
    private $repo;

    /** @var SessionInterface */
    private $session;

    /** @var Router */
    private $router;

    public function __construct(
        EntityManager $em,
        IdentityProviderRepository $repo,
        SessionInterface $session,
        Router $router
    ) {
        $this->em = $em;
        $this->repo = $repo;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @param string $providerUrl
     * @param boolean $create
     * @return \OpenIDConnectClient
     */
    public function getOpenIDConnectClient($providerUrl = null, $create = true)
    {
        if ($providerUrl === null) {
            $providerUrl = $this->session->get('provider_url');
        }

        $oidc = new \OpenIDConnectClient($providerUrl, null, null, $this->session);
        $oidc->setRedirectURL($this->getCallbackUrl());
        $oidc->addScope('email');
        $oidc->addScope('openid');

        $existing = $this->getProviderByUrl($providerUrl);
        if ($existing instanceof IdentityProvider) {
            $oidc->setClientID($existing->getClientId());
            $oidc->setClientSecret($existing->getClientSecret());
        } elseif ($create) {
            $this->registerClient($oidc);
        }

        return $oidc;
    }

    private function getCallbackUrl()
    {
        return $this->router->generate(
            'oidc_callback',
            array(),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $providerUrl
     * @return null|IdentityProvider
     */
    public function getProviderByUrl($providerUrl)
    {
        return $this->repo->findOneBy(compact('providerUrl'));
    }

    private function registerClient(\OpenIDConnectClient $oidc)
    {
        $oidc->register();
        $provider = new IdentityProvider();
        $provider
            ->setClientId($oidc->getClientID())
            ->setClientSecret($oidc->getClientSecret())
            ->setProviderUrl($oidc->getProviderURL());

        $this->em->persist($provider);
        $this->em->flush($provider);
    }

    /**
     * @return null|IdentityProvider
     */
    public function getDefault()
    {
        return $this->repo->findOneBy(['isDefault' => true]);
    }
}
