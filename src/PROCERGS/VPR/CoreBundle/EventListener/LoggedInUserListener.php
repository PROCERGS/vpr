<?php

namespace PROCERGS\VPR\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;

class LoggedInUserListener
{

    private $context;
    private $router;
    private $session;

    public function __construct(SecurityContextInterface $context,
                                RouterInterface $router,
                                SessionInterface $session)
    {
        $this->context = $context;
        $this->router = $router;
        $this->session = $session;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }
        $user = $this->context->getToken();
        if (null === ($token = $this->context->getToken()) || null === ($user = $token->getUser()) || !$this->context->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }
        $_route = $event->getRequest()->attributes->get('_route');
        if ($_route == 'fos_user_security_login') {
            $key = '_security.main.target_path'; #where "main" is your firewall name
            //check if the referer session key has been set
            if ($this->session->has($key)) {
                //set the url based on the link they were trying to access before being authenticated
                $url = $this->session->get($key);
        
                //remove the session key
                $this->session->remove($key);
            } else {
                $url = $this->router->generate('procergsvpr_core_homepage');
            }
            $event->setResponse(new RedirectResponse($url));
        }
        $t1 = ($_route == 'vpr_city_selection');
        $t2 = (null === $user->getTreVoter() && null === $user->getCity());
        if ($t2 && !$t1) {
            $url = $this->router->generate('vpr_city_selection');
            $event->setResponse(new RedirectResponse($url));
        } elseif (!$t2 && $t1) {
            $url = $this->router->generate('procergsvpr_core_homepage');
            $event->setResponse(new RedirectResponse($url));
        }
    }

}
