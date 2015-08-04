<?php

namespace PROCERGS\VPR\CoreBundle\Security;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class AuthenticationHandler implements LogoutSuccessHandlerInterface
{
    protected $router;

    public function setRouter(RouterInterface $var)
    {
        $this->router = $var;
    }

    public function onLogoutSuccess(Request $request)
    {
        //procergsvpr_core_end_offer
        if ($request->get('home')) {
            return new RedirectResponse($this->router->generate('procergsvpr_core_homepage'));
        }
        $url = $this->router->generate('procergsvpr_core_end');
        return new RedirectResponse($url);
    }
}
