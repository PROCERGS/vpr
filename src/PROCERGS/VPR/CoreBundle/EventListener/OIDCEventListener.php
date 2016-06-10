<?php

namespace PROCERGS\VPR\CoreBundle\EventListener;

use Donato\OIDCBundle\Event\FilterResponseEvent;
use Donato\OIDCBundle\Security\Authentication\Token\OIDCToken;
use PROCERGS\VPR\CoreBundle\Entity\UserManager;
use PROCERGS\VPR\CoreBundle\Exception\OIDC\AccessDeniedException;
use PROCERGS\VPR\CoreBundle\Exception\OIDC\UserNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class OIDCEventListener implements EventSubscriberInterface
{

    /** @var TokenStorage */
    protected $tokenStorage;

    /** @var UserManager */
    protected $userManager;

    /** @var RouterInterface */
    protected $router;

    /** @var Session */
    protected $session;

    /**
     * OIDCEventListener constructor.
     * @param TokenStorage $tokenStorage
     * @param UserManager $userManager
     * @param RouterInterface $router
     */
    public function __construct(
        TokenStorage $tokenStorage,
        UserManager $userManager,
        RouterInterface $router,
        Session $session
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
        $this->router = $router;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array(
                array('onUserNotFound', 100),
            ),
        );
    }

    public function onFilterResponse(FilterResponseEvent $event)
    {
        $this->userManager->checkEmailVerified($event, $this->router);
    }

    public function onUserNotFound(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();

        if ($e instanceof UserNotFoundException ||
            $e instanceof AccessDeniedException ||
            $e instanceof \OpenIDConnectClientException
        ) {
            $this->session->getFlashBag()->add('danger', $e->getMessage());

            $url = $this->router->generate('admin_login');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
