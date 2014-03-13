<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**     
     * @Template()
     */
    public function indexAction()
    {
        $voter = $this->get('security.context')->getToken()->getUser();
        return array('voter' => $voter);
    }
    
    /**
     * @Template()
     */    
    public function municipioAction()
    {
        $voter = $this->get('security.context')->getToken()->getUser();
        return array('voter' => $voter);
    }

}
