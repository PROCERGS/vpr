<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Form\Type\CitySelectionType;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use JMS\Serializer\SerializationContext;
use PROCERGS\VPR\CoreBundle\Entity\City;
use PROCERGS\VPR\CoreBundle\Exception\FormException;

class PersonController extends Controller
{

    /**
     * @Route("/city/select", name="vpr_city_selection")
     * @Template()
     */
    public function setCityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $this->getUser();
        $translator = $this->get('translator');
        $session = $this->get('session');
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        $data = array();
        if ($person->getTreVoter() instanceof TREVoter) {
            $data['voterRegistration'] = $person->getTreVoter()->getId();
        }
        $form = $this->createForm(new CitySelectionType());
        $form->setData($data);

        $form->handleRequest($request);

        try {
            if ($form->isValid()) {
                $data = $form->getData();

                if (array_key_exists('voterRegistration', $data) && strlen(trim($data['voterRegistration'])) > 0) {
                    $voterRegistration = trim($data['voterRegistration']);
                    $treRepo = $em->getRepository('PROCERGSVPRCoreBundle:TREVoter');
                    $voter = $treRepo->findOneBy(array('id' => $voterRegistration));
                    if (!($voter instanceof TREVoter)) {
                        $message = $translator->trans('form.city-selection.voter-not-found',
                                array(), 'validators');
                        $formError = new FormError($message);
                        $form->get('voterRegistration')->addError($formError);
                        throw new FormException($formError);
                    }
                    $person->setTreVoter($voter);
                    $city = $voter->getCity();
                }

                $cityName = trim($data['city']['name']);
                if (strlen($cityName) > 0) {
                    $typedCity = $cityRepo->findOneBy(array('name' => $cityName));
                    if (!($typedCity instanceof City)) {
                        $message = $translator->trans('form.city-selection.city-not-found',
                                array(), 'validators');
                        $formError = new FormError($message);
                        $form->get('city')->get('name')->addError($formError);
                        throw new FormException($formError);
                    } elseif (isset($city) && $city instanceof City) {
                        // user is trying to use a different city!
                        $message = $translator->trans('form.city-selection.tried-different-city');
                        $session->getFlashBag()->add('notice', $message);
                    } else {
                        $city = $typedCity;
                    }
                }
                $person->setCity($city);
                $this->get('session')->set('city_id', $city->getId());
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($person);

                $url = $this->generateUrl('procergsvpr_core_homepage');
                return $this->redirect($url);
            }
        } catch (FormException $e) {

        }

        $serializer = $this->container->get('jms_serializer');
        $cities = $serializer->serialize($cityRepo->findAll(), 'json',
                SerializationContext::create()
                        ->setSerializeNull(true)
                        ->setGroups(array('autocomplete')));

        return array('form' => $form->createView(), 'cities' => $cities);
    }

}
