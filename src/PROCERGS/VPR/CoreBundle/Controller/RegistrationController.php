<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormError;
use PROCERGS\VPR\CoreBundle\Entity\Person;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationMismatchException;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationNotFoundException;
use PROCERGS\VPR\CoreBundle\Exception\TREVoterException;

class RegistrationController extends Controller
{

    /**
     * @Route("/register", name="vpr_register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $this->handleErrors($request);

        $formFactory = $this->get('fos_user.registration.form.factory');

        $user = new Person();
        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $response = $this->handleRegistration($request, $form, $user);
            if ($response instanceof Response) {
                return $response;
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    private function handleErrors($request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            $session->getFlashBag()->add('danger',
                    $this->get('translator')->trans($error->getMessage()));
        }
    }

    public function handleRegistration($request, $form, $user)
    {
        $form->handleRequest($request);
        if (!$form->isValid()) {
            return;
        }
        $userManager = $this->get('fos_user.user_manager');

        // TODO: prepare voter registration
        $voterRegistration = str_pad($form->get('username')->getData(), 12, '0',
                STR_PAD_LEFT);
        $firstName = $form->get('firstname')->getData();

        $treVoterSearch = new TREVoter();
        $treVoterSearch->setId($voterRegistration);
        $userFromVoterRegistration = $userManager->findUserBy(array(
            'treVoter' => $treVoterSearch,
            'loginCidadaoAccessToken' => null
        ));
        try {
            $response = $this->handleUserFromVoterRegistration($userFromVoterRegistration,
                    $firstName);
            if ($response instanceof Response) {
                return $response;
            } else {
                return $this->newUserFromVoterRegistration($voterRegistration,
                                $user, $userManager);
            }
        } catch (TREVoterException $e) {
            $form->addError(new FormError($this->get('translator')->trans($e->getMessage())));
        }
    }

    private function authenticate($user, $response)
    {
        try {
            $loginManager = $this->get('fos_user.security.login_manager');
            $firewallName = $this->container->getParameter('fos_user.firewall_name');
            $loginManager->loginUser($firewallName, $user, $response);
        } catch (AccountStatusException $e) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }

    private function checkNamesEqual($name1, $name2)
    {
        $name1 = explode(' ', $name1);
        $name2 = explode(' ', $name2);
        $firstName1 = reset($name1);
        $firstName2 = reset($name2);
        return (mb_strtolower(trim($firstName1)) === mb_strtolower(trim($firstName2)));
    }

    private function handleUserFromVoterRegistration($user, $firstName)
    {
        if (!($user instanceof UserInterface)) {
            return;
        }

        if ($this->checkNamesEqual($firstName, $user->getFirstName())) {
            $response = $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
            $this->authenticate($user, $response);
            return $response;
        } else {
            throw new VoterRegistrationMismatchException('register.voter_registration.mismatch');
        }
    }

    private function newUserFromVoterRegistration($voterRegistration,
                                                  Person $user, $userManager)
    {
        $treRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:TREVoter');
        $treVoter = $treRepo->findOneBy(array('id' => $voterRegistration));

        if (!($treVoter instanceof TREVoter)) {
            throw new VoterRegistrationNotFoundException('register.voter_registration.notfound');
        }

        if (!$this->checkNamesEqual($treVoter->getName(), $user->getFirstName())) {
            throw new VoterRegistrationMismatchException('register.voter_registration.mismatch');
        }

        $user->setUsername(uniqid(mt_rand(), true))
                ->setTreVoter($treVoter)
                ->setCity($treVoter->getCity());
        $userManager->updateUser($user);

        $response = $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        $this->authenticate($user, $response);
        return $response;
    }

}
