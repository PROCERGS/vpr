<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\Serializer\SerializationContext;
use PROCERGS\VPR\CoreBundle\Entity\StatsOptionVote;
use PROCERGS\VPR\CoreBundle\Entity\Vote;

class PollOptionController extends Controller
{

    /**
     * @Route("/ballot", name="vpr_ballotByCity")
     * @Template()
     */
    public function viewByCityAction(Request $request)
    {
        $form = $this->createFormBuilder()->add('city', 'text',
                        array(
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
                $pollOptions = $pollOptionsRepo->findByPollCorede($poll,
                        $city->getCorede());

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
        $cities = $serializer->serialize($cityRepo->findAll(), 'json',
                SerializationContext::create()->setSerializeNull(true)->setGroups(array(
                    'autocomplete'
        )));

        return compact('form', 'options', 'cities', 'categoriesId', 'corede');
    }

    /**
     * @Route("/step/{stepId}/corede/{coredeId}", name="procergsvpr_step")
     * @Template()
     */
    public function stepAction(Request $request, $stepId, $coredeId)
    {
        $em = $this->getDoctrine()->getManager();
        $votingSession = $this->get('vpr_voting_session_provider');
        try {
            $vote = $this->validateVotingSession($request, $votingSession,
                    $stepId, $coredeId);

            $formBuilder = $this->createFormBuilder()->getForm();
            $formBuilder->handleRequest($request);
            if ($formBuilder->isValid()) {
                return $this->handleStep($request, $votingSession, $em, $vote);
            }
        } catch (AccessDeniedHttpException $e) {
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        }


        $repository = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $poll = $em->merge($vote->getBallotBox()->getPoll());
        $corede = $em->merge($vote->getCorede());
        $step = $em->merge($vote->getLastStep());

        $pollOptions = $repository->findByPollCoredeStep($poll, $corede, $step);

        $options = array();
        $categoriesId = array();
        foreach ($pollOptions as $option) {
            $options[$option->getStep()->getName()][$option->getCategory()->getName()][] = $option;
            $categoriesId[$option->getCategory()->getName()] = $option->getCategory()->getId();
        }

        $form = $formBuilder->createView();
        return compact('form', 'corede', 'step', 'options', "categoriesId");
    }

    private function validateVotingSession(Request $request, $votingSession,
                                           $stepId, $coredeId)
    {
        $session = $request->getSession();
        $vote = $votingSession->requireLastStep();

        if ($vote->getLastStep()->getId() != $stepId || $vote->getCorede()->getId() != $coredeId) {
            $session->getFlashBag()->add('danger',
                    $this->get('translator')->trans('voting.session.invalid'));
            throw new AccessDeniedHttpException();
        }

        return $vote;
    }

    private function handleStep(Request $request, $votingSession, $em,
                                Vote $vote)
    {
        $pollOptionRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $stepRepo = $em->getRepository('PROCERGSVPRCoreBundle:Step');
        $poll = $em->merge($vote->getBallotBox()->getPoll());
        $corede = $em->merge($vote->getCorede());
        $step = $em->merge($vote->getLastStep());

        $options = $request->get('options');
        if (!$pollOptionRepo->checkStepOptions($step, $options)) {
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        }
        $vote->addPollOption($options);
        $nextStep = $stepRepo->getNextPollStep($vote);
        if ($nextStep) {
            $vote->setLastStep($nextStep);
            $votingSession->updateVote($vote);
            $params = array(
                'stepId' => $vote->getLastStep()->getId(),
                'coredeId' => $vote->getCorede()->getId()
            );
            $url = $this->generateUrl('procergsvpr_step', $params);
            return $this->redirect($url);
        }
        $votingSession->persistVote();

        $this->registerStats($em, $options, $corede, $poll, $vote);

        $em->flush();
        $vote->setLastStep(null);
        $vote->setPollOption(null);
        $votingSession->updateVote($vote);
        return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
    }

    private function registerStats($em, $options, $corede, $poll, $vote)
    {
        $hasLoginCidadao = ($vote->getLoginCidadaoId()) ? true : false;
        $hasVoterRegistration = ($vote->getVoterRegistration()) ? true : false;

        foreach ($options as $option) {
            $stats = new StatsOptionVote();
            $stats->setCoredeId($corede->getId());
            $stats->setPollId($poll->getId());
            $stats->setPollOptionId($option->getId());
            $stats->setHasLoginCidadao($hasLoginCidadao);
            $stats->setHasVoterRegistration($hasVoterRegistration);
            $stats->setCreatedAt(new \DateTime());
            $em->persist($stats);
        }
    }

}
