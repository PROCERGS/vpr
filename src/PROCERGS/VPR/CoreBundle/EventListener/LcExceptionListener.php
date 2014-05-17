<?php
namespace PROCERGS\VPR\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use PROCERGS\VPR\CoreBundle\Exception\LcException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LcExceptionListener
{

    protected $router;

    protected $translator;

    protected $session;

    public function setRouter(RouterInterface $var)
    {
        $this->router = $var;
        return $this;
    }

    public function setTrans(TranslatorInterface $var)
    {
        $this->translator = $var;
        return $this;
    }

    public function setSession(SessionInterface $var)
    {
        $this->session = $var;
        return $this;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof LcException) {
            $this->session->getFlashBag()->add('danger', $this->translator->trans($exception->getMessage(), array(
                '%field%' => $this->translator->trans($exception->getPlaceHolder())
            )));
            $url = $this->router->generate('procergsvpr_core_homepage');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}