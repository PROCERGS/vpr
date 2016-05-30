<?php

namespace AppBundle\EventListener;

use Donato\OIDCBundle\Event\FilterResponseEvent;
use Donato\OIDCBundle\Security\Authentication\Token\OIDCToken;
use PROCERGS\VPR\CoreBundle\Exception\OIDC\AccessDeniedException;
use PROCERGS\VPR\CoreBundle\Exception\OIDC\UserNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class OIDCEventListener
{

    /** @var TokenStorage */
    protected $tokenStorage;

    /** @var RouterInterface */
    protected $router;

    /**
     * OIDCEventListener constructor.
     * @param TokenStorage $tokenStorage
     * @param RouterInterface $router
     */
    public function __construct(
        TokenStorage $tokenStorage,
        RouterInterface $router
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    public function onFilterResponse(FilterResponseEvent $event)
    {
        $emailVerified = $this->isEmailVerified();

        if ($emailVerified) {
            $url = $this->router->generate('auth_index', [], RouterInterface::ABSOLUTE_URL);
            $callback = $this->router->generate('auth_oidc_callback', compact('url'));
            $event->setResponse(new RedirectResponse($callback));

            return;
        }

        $callback = $this->router->generate('auth_oidc_callback', compact('url'), RouterInterface::ABSOLUTE_URL);
        $event->setResponse(new RedirectResponse($callback));
    }

    public function onUserNotFound(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();

        if ($e instanceof UserNotFoundException) {
            //
        }

        if ($e instanceof AccessDeniedException) {
            //
        }
    }
}