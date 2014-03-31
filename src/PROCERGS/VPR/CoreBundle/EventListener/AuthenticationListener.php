<?php

namespace PROCERGS\VPR\CoreBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Security\LoginManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationListener implements EventSubscriberInterface
{

    private $loginManager;
    private $firewallName;
    protected $container;

    public function setContainer($var)
    {
        $this->container = $var;
    }

    public function __construct(LoginManagerInterface $loginManager, $firewallName)
    {
        $this->loginManager = $loginManager;
        $this->firewallName = $firewallName;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize'
        );
    }

    public function onRegistrationInitialize($event)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = new \PROCERGS\VPR\CoreBundle\Entity\Person;
        $request = $event->getRequest();
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $user = $userManager->findUserByUsername($form->get('username')->getData());
                if (!$user) {
                    return;
                }
                try {
                    $this->loginManager->loginUser($this->firewallName, $user, $event->getResponse());

                    $event->getDispatcher()->dispatch(FOSUserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($user, $event->getRequest()));

                    $url = $this->container->get('router')->generate('procergsvpr_core_homepage');
                    $event->setResponse(new RedirectResponse($url));
                } catch (AccountStatusException $ex) {
                    // We simply do not authenticate users which do not pass the user
                    // checker (not enabled, expired, etc.).
                }
            }
        }
    }

}