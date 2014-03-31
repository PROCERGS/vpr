<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Form\Type\CitySelectionType;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{

    /**
     * @Route("/city/select", name="vpr_city_selection")
     * @Template()
     */
    public function setCityAction(Request $request)
    {
        $form = $this->createForm(new CitySelectionType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            print_r($data);
            die();
        }
        return array('form' => $form->createView());
    }

}
