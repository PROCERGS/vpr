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
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $person = $this->getUser();

            if (array_key_exists('voterRegistration', $data)) {
                $voterRegistration = $data['voterRegistration'];
                $treRepo = $em->getRepository('PROCERGSVPRCoreBundle:TREVoter');
                $voter = $treRepo->findOneBy(compact('voterRegistration'));
                $city = $voter->getCity();
            } else {
                $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
                $city = $cityRepo->findOneBy(array('name' => $data['city']['name']));
            }

            $person->setCity($city);
            $this->get('session')->set('city_id', $city->getId());
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($person);

            $url = $this->generateUrl('procergsvpr_core_cedula');
            return $this->redirect($url);
        }
        return array('form' => $form->createView());
    }

}
