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
            ->add('city', 'text', array(
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
            $city = $cityRepo->findOneBy(array(
                'name' => $data['city']
            ));
            
            if ($city) {
                $pollRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll');
                $pollOptionsRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
                
                $poll = $pollRepo->findActivePoll();
                $pollOptions = $pollOptionsRepo->findByPollCorede($poll, $city->getCorede());
                
                foreach ($pollOptions as $option) {
                    $step = $option->getStep()->getName();
                    
                    if (! array_key_exists($step, $steps)) {
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
        $cities = $serializer->serialize($cityRepo->findAll(), 'json', SerializationContext::create()->setSerializeNull(true)
            ->setGroups(array(
            'autocomplete'
        )));
        
        return compact('form', 'steps', 'cities');
    }

    /**
     * @Route("/step/{id}", name="procergsvpr_step")
     * @Template()
     */
    public function stepAction($id)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $vote = $session->get('vote');
        if (! $vote) {
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        }
        $em = $this->getDoctrine()->getManager();
        if (!$vote->getLastStep() || $vote->getLastStep()->getId() != $id) {
            return 'do something mutley';
        }
        $pollOptionRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $poll = $em->merge($vote->getBallotBox()
            ->getPoll());
        $corede = $em->merge($vote->getCorede());
        $step = $em->merge($vote->getLastStep());
        
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $options = $request->get('options');
            if (! $pollOptionRepo->checkStepOptions($step, $options)) {
                return 'do something mutley';
            }
            $vote->addPollOption($options);
            $b = $pollOptionRepo->getNextPollStep($vote);
            if ($b) {
                $vote->setLastStep($b);
                $session->set('vote', $vote);
                return $this->redirect($this->generateUrl('procergsvpr_step', array(
                    'id' => $vote->getLastStep()
                        ->getId()
                )));
            }
            $serializer = $this->container->get('jms_serializer');
            $options = $pollOptionRepo->getPollOption($vote);
            $serializedOptions = $serializer->serialize($options, 'json', SerializationContext::create()->setSerializeNull(true)
                ->setGroups(array(
                'vote'
            )));
            $vote->setPlainOptions($serializedOptions);
            $vote->finishMe();
            $vote->setLastStep(null);
            $em->persist($vote);
            $em->flush();
            $session->set('vote', $vote);
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));            
        }
        $pollOptions = $pollOptionRepo->findByPollCoredeStep($poll, $corede, $step);
        
        $options = array();
        foreach ($pollOptions as $option) {
            $options[$option->getStep()->getName()][$option->getCategory()->getName()][] = $option;
        }
        
        $form = $form->createView();
        return compact('form', 'pollOptions', 'step', 'options');
    }
}
