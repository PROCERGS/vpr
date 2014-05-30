<?php
namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use PROCERGS\VPR\CoreBundle\Entity\StatsOptionVote;

class PollOptionController extends Controller
{

    /**
     * @Route("/ballot", name="vpr_ballotByCity")
     * @Template()
     */
    public function viewByCityAction(Request $request)
    {
        $form = $this->createFormBuilder()->add('city', 'text', array(
            'required' => true,
            'label' => 'form.city.select'
        ))->add('submit', 'submit')->getForm();

        $form->handleRequest($request);

        $options = array();
        $categoriesId = array();

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
                    $options[$option->getStep()->getName()][$option->getCategory()->getName()][] = $option;
                    $categoriesId[$option->getCategory()->getName()] = $option->getCategory()->getId();
                }

                $corede = $city->getCorede();
            }
        }

        $form = $form->createView();

        $serializer = $this->container->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        $cities = $serializer->serialize($cityRepo->findAll(), 'json', SerializationContext::create()->setSerializeNull(true)->setGroups(array(
            'autocomplete'
        )));

        return compact('form', 'options', 'cities', 'categoriesId', 'corede');
    }

    /**
     * @Route("/step/{stepid}/corede/{coredeid}", name="procergsvpr_step")
     * @Template()
     */
    public function stepAction($stepid, $coredeid)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $vote = $session->get('vote');
        if (! $vote || ! $vote->getLastStep()) {
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        }
        $em = $this->getDoctrine()->getManager();
        if ($vote->getLastStep()->getId() != $stepid || $vote->getCorede()->getId() != $coredeid) {
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        }
        $pollOptionRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $poll = $em->merge($vote->getBallotBox()->getPoll());
        $corede = $em->merge($vote->getCorede());
        $step = $em->merge($vote->getLastStep());

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $options = $request->get('options');
            if (! $pollOptionRepo->checkStepOptions($step, $options)) {
                return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
            }
            $vote->addPollOption($options);
            $b = $pollOptionRepo->getNextPollStep($vote);
            if ($b) {
                $vote->setLastStep($b);
                $session->set('vote', $vote);
                return $this->redirect($this->generateUrl('procergsvpr_step', array(
                    'stepid' => $vote->getLastStep()->getId(),
                    'coredeid' => $vote->getCorede()->getid()
                )));
            }
            $serializer = $this->container->get('jms_serializer');
            $options = $pollOptionRepo->getPollOption($vote);
            $serializedOptions = $serializer->serialize($options, 'json', SerializationContext::create()->setSerializeNull(true)->setGroups(array(
                'vote'
            )));
            // $ballotBox = $em->merge($vote->getBallotBox());
            // $vote->setBallotBox($ballotBox->setPoll($poll));
            $vote->setPlainOptions($serializedOptions);
            $vote->finishMe();
            $vote->setCreatedAtValue();
            $vote = $em->merge($vote);
            $em->persist($vote);

            $hasLoginCidadao = ($vote->getLoginCidadaoId()) ? true : false;
            $hasVoterRegistration = ($vote->getVoterRegistration()) ? true : false;
            foreach($options as $option){
                $stats = new StatsOptionVote();
                $stats->setCoredeId($corede->getId());
                $stats->setPollId($poll->getId());
                $stats->setPollOptionId($option->getId());
                $stats->setHasLoginCidadao($hasLoginCidadao);
                $stats->setHasVoterRegistration($hasVoterRegistration);
                $stats->setCreatedAt(new \DateTime());
                $em->persist($stats);
            }

            $em->flush();
            $vote->setLastStep(null);
            $vote->setPollOption(null);
            $session->set('vote', $vote);
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        }
        $pollOptions = $pollOptionRepo->findByPollCoredeStep($poll, $corede, $step);

        $options = array();
        $categoriesId = array();
        foreach ($pollOptions as $option) {
            $options[$option->getStep()->getName()][$option->getCategory()->getName()][] = $option;
            $categoriesId[$option->getCategory()->getName()] = $option->getCategory()->getId();
        }

        $form = $form->createView();
        return compact('form', 'corede', 'step', 'options', "categoriesId");
    }
}
