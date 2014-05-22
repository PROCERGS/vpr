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
use PROCERGS\VPR\CoreBundle\Exception\TREVoterException;

class ExceptionListener
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
            if ($exception->getPlaceHolder()) {
                $this->session->getFlashBag()->add('danger', $this->translator->trans($exception->getMessage(), array(
                    '%field%' => $this->translator->trans($exception->getPlaceHolder())
                )));
            } else {
                $this->session->getFlashBag()->add('danger', $this->translator->trans($exception->getMessage()));
            }
            $url = $this->router->generate('procergsvpr_core_homepage');
            $event->setResponse(new RedirectResponse($url));
        } elseif ($exception instanceof TREVoterException) {
            $this->session->getFlashBag()->add('danger', $this->translator->trans($exception->getMessage()));
            $url = $event->getRequest()->headers->get('referer');
            if (!$url) {
                $url = $this->router->generate('procergsvpr_core_homepage');
            }
            $event->setResponse(new RedirectResponse($url));
        }
    }
}