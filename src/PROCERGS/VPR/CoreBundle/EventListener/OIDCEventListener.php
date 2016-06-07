<?php

namespace PROCERGS\VPR\CoreBundle\EventListener;

use Donato\OIDCBundle\Event\FilterResponseEvent;
use Donato\OIDCBundle\Security\Authentication\Token\OIDCToken;
use PROCERGS\VPR\CoreBundle\Entity\UserManager;
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

    /** @var UserManager */
    protected $userManager;

    /** @var RouterInterface */
    protected $router;

    /**
     * OIDCEventListener constructor.
     * @param TokenStorage $tokenStorage
     * @param UserManager $userManager
     * @param RouterInterface $router
     */
    public function __construct(
        TokenStorage $tokenStorage,
        UserManager $userManager,
        RouterInterface $router
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
        $this->router = $router;
    }

    public function onFilterResponse(FilterResponseEvent $event)
    {
        $this->userManager->checkEmailVerified($event, $this->router);
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