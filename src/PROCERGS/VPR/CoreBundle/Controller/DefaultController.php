<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        $voter = $this->get('security.context')->getToken()->getUser();

        return $this->render('PROCERGSVPRCoreBundle:Default:index.html.twig', array('voter' => $voter));
    }

}
