<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PersonController extends Controller
{

    /**
     * @Route("/city/select", name="vpr_city_selection")
     * @Template()
     */
    public function setCityAction()
    {
        return array();
    }

}
