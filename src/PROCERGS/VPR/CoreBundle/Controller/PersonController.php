<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Form\Type\CitySelectionType;

class PersonController extends Controller
{

    /**
     * @Route("/city/select", name="vpr_city_selection")
     * @Template()
     */
    public function setCityAction()
    {
        $form = $this->createForm(new CitySelectionType());
        return array('form' => $form->createView());
    }

}
