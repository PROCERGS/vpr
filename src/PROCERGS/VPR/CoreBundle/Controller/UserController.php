<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Donato\OIDCBundle\Entity\IdentityProviderManager;
use PROCERGS\VPR\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/confirm/{code}", name="user_confirm")
     * @Template()
     */
    public function confirmAction(Request $request, $code)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!($user instanceof User)) {
            $key = '_security.main.target_path';
            $request->getSession()->set($key, $request->getRequestUri());

            return $this->redirectToRoute('admin_login');
        }

        if ($user->getConfirmationCode() === $code) {
            $user->setConfirmationCode(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush($user);

            if ($user->getConfirmationCode() === null) {
                $this->addFlash('success', 'account.activated');

                return $this->redirectToRoute('admin');
            }
        }

        throw $this->createAccessDeniedException('Invalid code');
    }

}
