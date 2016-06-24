<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Form\FormError;

class BallotBoxController extends Controller
{

    /**
     * @Route("/places", name="vpr_list_ballotboxes")
     * @Template()
     */
    public function listByCityAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('city', 'city', array('required' => true, 'label' => 'Type your city'))
            ->add('submit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        $boxes = array();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
            $city = $cityRepo->findOneBy(array('name' => $data['city']));
            if ($city) {
                $pollRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll');
                $ballotBoxRepo = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox');

                $poll = $pollRepo->findActivePoll();
                $boxes = $ballotBoxRepo->findBy(
                    array(
                        'city' => $city,
                        'poll' => $poll,
                    )
                );
            } else {
                $translator = $this->get('translator');
                $message = $translator->trans(
                    'form.city-selection.city-not-found',
                    array(),
                    'validators'
                );
                $form->addError(new FormError($message));
            }
        }

        $form = $form->createView();

        $serializer = $this->container->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        $cities = $serializer->serialize(
            $cityRepo->findAll(),
            'json',
            SerializationContext::create()
                ->setSerializeNull(true)
                ->setGroups(array('autocomplete'))
        );

        return compact('form', 'boxes', 'cities');
    }

}
