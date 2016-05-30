<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use PROCERGS\VPR\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Route("/confirm/{code}")
     * @Template()
     */
    public function confirmAction($code)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getConfirmationCode() === $code) {
            die("ok");
        }
        throw $this->createAccessDeniedException('Invalid code');
    }

}
