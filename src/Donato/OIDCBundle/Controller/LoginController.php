<?php

namespace Donato\OIDCBundle\Controller;

use Donato\OIDCBundle\Event\DonatoOIDCEvents;
use Donato\OIDCBundle\Event\FilterOIDCClientEvent;
use Donato\OIDCBundle\Event\FilterOIDCTokenEvent;
use Donato\OIDCBundle\Event\FilterRequestEvent;
use Donato\OIDCBundle\Event\FilterResponseEvent;
use Donato\OIDCBundle\Form\IdentityProviderType;
use Donato\OIDCBundle\Security\OIDCUserProvider;
use PROCERGS\VPR\CoreBundle\Entity\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Donato\OIDCBundle\Security\Authentication\Token\OIDCToken;
use Donato\OIDCBundle\Entity\IdentityProvider;
use PROCERGS\VPR\CoreBundle\Entity\User;

class LoginController extends Controller
{

    /**
     * @Route("/askProvider", name="oidc_login")
     */
    public function askProviderAction(Request $request)
    {
        $providerId = $request->get('providerId');

        if ($providerId > 0) {
            $provider = $this->getDoctrine()->getRepository('DonatoOIDCBundle:IdentityProvider')->find($providerId);
        } else {
            $provider = new IdentityProvider();
        }

        $formType = 'Donato\OIDCBundle\Form\IdentityProviderType';
        $formType = new IdentityProviderType();
        $form = $this->createForm($formType, $provider);

        $form->handleRequest($request);
        if ($form->isValid() || $providerId > 0) {
            $oidc = $this->getOpenIDConnectClient($provider->getProviderUrl());
            $request->getSession()->set('provider_url', $oidc->getProviderURL());
            $request->getSession()->save();
            $oidc->authenticate();

            die("ok");
        }

        return $this->render(
            'DonatoOIDCBundle:Login:ask_provider.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/callback", name="oidc_callback")
     */
    public function callbackAction(Request $request)
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get("event_dispatcher");
        $filterRequestEvent = new FilterRequestEvent($request);
        $dispatcher->dispatch(DonatoOIDCEvents::OIDC_BEFORE_CALLBACK, $filterRequestEvent);
        $request = $filterRequestEvent->getRequest();

        $providerUrl = $request->getSession()->get('provider_url');
        $oidc = $this->getOpenIDConnectClient($providerUrl);

        $filterOidcClientEvent = new FilterOIDCClientEvent($oidc);
        $dispatcher->dispatch(DonatoOIDCEvents::OIDC_BEFORE_AUTH, $filterOidcClientEvent);
        $oidc = $filterOidcClientEvent->getOpenIDConnectClient();

        $oidc->authenticate();
        $sub = $oidc->requestUserInfo('sub');
        $idp = $this->getIdentityProvider($providerUrl);
        $fullSub = "{$idp->getId()}#$sub";

        $user = $this->prepareUser($fullSub);
        new User($fullSub);
        $provider = $this->getIdPManager()->getProviderByUrl($providerUrl);
        $token = $this->prepareToken($user, $provider, $oidc->getAccessToken());

        $filterOIDCTokenEvent = new FilterOIDCTokenEvent($token, $user, $provider, $oidc);
        $dispatcher->dispatch(DonatoOIDCEvents::OIDC_FILTER_TOKEN);
        $token = $filterOIDCTokenEvent->getToken();

        $this->get('security.token_storage')->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);

        $response = $this->redirectToRoute('admin');
        $filterResponseEvent = new FilterResponseEvent($response);
        $dispatcher->dispatch(DonatoOIDCEvents::OIDC_FILTER_RESPONSE, $filterResponseEvent);
        $response = $filterResponseEvent->getResponse();

        $request->getSession()->set('sub', $sub);

        return $response;
    }

    /**
     * @param string $providerUrl
     * @param boolean $create
     * @return \OpenIDConnectClient
     */
    private function getOpenIDConnectClient($providerUrl, $create = true)
    {
        $manager = $this->getIdPManager();

        return $manager->getOpenIDConnectClient($providerUrl, $create);
    }

    private function getIdentityProvider($providerUrl)
    {
        $manager = $this->getIdPManager();

        return $manager->getProviderByUrl($providerUrl);
    }

    /**
     * @return \Donato\OIDCBundle\Entity\IdentityProviderManager
     */
    private function getIdPManager()
    {
        return $this->get('oidc.idp.manager');
    }

    /**
     * @param User $user
     * @param IdentityProvider $provider
     * @param string $accessToken
     * @return OIDCToken
     */
    private function prepareToken(User $user, IdentityProvider $provider, $accessToken)
    {
        $token = new OIDCToken($accessToken, $user->getRoles());
        $token->setUser($user);
        $token->setIdentityProvider($provider);
        $token->setAuthenticated(true);

        return $token;
    }

    private function prepareUser($sub)
    {
        /** @var OIDCUserProvider $userProvider */
        $userProvider = $this->get('');

        try {
            $user = $userProvider->loadUserByUsername($sub);
        } catch (UsernameNotFoundException $e) {
            $em = $this->getDoctrine()->getEntityManager();
            $user = new User($sub);

            $em->persist($user);
            $em->flush($user);
        }

        return $user;
    }
}
