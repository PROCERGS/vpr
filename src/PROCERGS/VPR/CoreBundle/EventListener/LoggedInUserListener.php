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
        if (is_null($this->context->getToken())) {
            return;
        }
        if (!is_null($this->session->get('city_id'))) {
            // City already chosen
            return;
        }

        $_route = $event->getRequest()->attributes->get('_route');
        if ($this->context->isGranted('IS_AUTHENTICATED_FULLY') && $_route !== 'vpr_city_selection') {
            $person = $this->context->getToken()->getUser();
            $treEntry = $person->getTREEntry();
            if (!($treEntry instanceof TREVoter)) {
                // Always sends users to select/confirm their voting city.
                // This avoids taking for granted that the user's city is correct
                $url = $this->router->generate('vpr_city_selection');
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }

}
