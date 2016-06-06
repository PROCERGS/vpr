<?php

namespace PROCERGS\VPR\CoreBundle\EventListener;

use PROCERGS\VPR\CoreBundle\Entity\User;
use PROCERGS\VPR\CoreBundle\Entity\UserManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use PROCERGS\VPR\CoreBundle\Entity\City;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class LoggedInUserListener
{

    const CITY_SELECTION_ROUTE = 'vpr_city_selection';
    const VPR_HOME_ROUTE = 'vpr_city_selection';

    /** @var TokenStorage */
    private $tokenStorage;

    /** @var AuthorizationChecker */
    private $authorizationChecker;

    /** @var RouterInterface */
    private $router;

    /** @var SessionInterface */
    private $session;

    /** @var UserManager */
    private $userManager;

    public function __construct(
        TokenStorage $tokenStorage,
        AuthorizationChecker $authorizationChecker,
        RouterInterface $router,
        SessionInterface $session,
        UserManager $userManager
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
        $this->session = $session;
        $this->userManager = $userManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }
        $user = $this->tokenStorage->getToken();
        if (null === ($token = $this->tokenStorage->getToken())
            || null === ($user = $token->getUser())
            || !$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')
        ) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');

        if ($user instanceof User) { // It's an OIDC user
            // TODO: fix compatibility with OIDC users here

            switch ($route) {
                case 'oidc_redirect':
                case 'oidc_check_email':
                    return;
            }

            if (false === $this->userManager->checkEmailVerified($event, $this->router)) {
                return;
            }

            $this->handleRedirectAfterLogin($event);

            return;
        }

        $isCitySelectionRoute = ($route === self::CITY_SELECTION_ROUTE);
        if (strpos($route, 'admin') !== 0) {
            $hasCity = $user->getCityOrTreCity() instanceof City;
            if (!$hasCity && !$isCitySelectionRoute) {
                $url = $this->router->generate(self::CITY_SELECTION_ROUTE);
                $event->setResponse(new RedirectResponse($url));

                return;
            }
        }

        if ($route == 'fos_user_security_login') {
            $key = '_security.main.target_path'; #where "main" is your firewall name
            //check if the referer session key has been set
            if ($this->session->has($key)) {
                //set the url based on the link they were trying to access before being authenticated
                $url = $this->session->get($key);

                //remove the session key
                $this->session->remove($key);
            } else {
                $url = $this->router->generate(self::VPR_HOME_ROUTE);
            }
            $event->setResponse(new RedirectResponse($url));
        }
    }

    private function handleRedirectAfterLogin(GetResponseEvent $event)
    {
        $key = '_security.main.target_path'; #where "main" is your firewall name
        //check if the referer session key has been set
        if ($this->session->has($key)) {
            //set the url based on the link they were trying to access before being authenticated
            $url = $this->session->get($key);

            //remove the session key
            $this->session->remove($key);

            $event->setResponse(new RedirectResponse($url));
        }
    }

}
