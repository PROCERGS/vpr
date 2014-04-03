<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

class PollOptionController extends Controller
{

    /**
     * @Route("/ballot", name="vpr_ballotByCity")
     * @Template()
     */
    public function viewByCityAction(Request $request)
    {
        $form = $this->createFormBuilder()
                ->add('city', 'text',
                        array(
                    'required' => true
                ))
                ->add('submit', 'submit')
                ->getForm();

        $form->handleRequest($request);

        $steps = array();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
            $city = $cityRepo->findOneBy(array('name' => $data['city']));

            if ($city) {
                $pollRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll');
                $pollOptionsRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');

                $poll = $pollRepo->findActivePoll();
                $pollOptions = $pollOptionsRepo->findByPollCorede($poll,
                        $city->getCorede());

                foreach ($pollOptions as $option) {
                    $step = $option->getStep()->getName();

                    if (!array_key_exists($step, $steps)) {
                        $steps[$step] = array();
                    }
                    array_push($steps[$step], $option);
                }
            }
        }

        $form = $form->createView();

        $serializer = $this->container->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        $cities = $serializer->serialize($cityRepo->findAll(), 'json',
                SerializationContext::create()
                        ->setSerializeNull(true)
                        ->setGroups(array('autocomplete')));

        return compact('form', 'steps', 'cities');
    }

}
