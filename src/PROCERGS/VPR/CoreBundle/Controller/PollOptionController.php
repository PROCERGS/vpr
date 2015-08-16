<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\Serializer\SerializationContext;
use PROCERGS\VPR\CoreBundle\Form\DataTransformer\CityToNameTransformer;
use PROCERGS\VPR\CoreBundle\Entity\StatsOptionVote;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Entity\City;

class PollOptionController extends Controller
{

    /**
     * @Route("/ballot/{cityId}", name="vpr_ballot_view")
     * @Template()
     */
    public function viewBallotAction(Request $request, $cityId)
    {
        $em       = $this->getDoctrine()->getManager();
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        if (is_numeric($cityId)) {
            $city = $cityRepo->find($cityId);
        } else {
            $city = $cityRepo->findOneByName($cityId);
        }

        if (!$city instanceof City) {
            throw new NotFoundHttpException();
        }

        $pollRepo        = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll');
        $pollOptionsRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');

        $poll        = $pollRepo->findLastPoll();
        $pollOptions = $pollOptionsRepo->findByPollCorede($poll,
            $city->getCorede());

        foreach ($pollOptions as $option) {
            $options[$option->getStep()->getName()][$option->getCategory()->getName()][]
                = $option;

            $categoriesId[$option->getCategory()->getName()] = $option->getCategory();
        }

        $corede = $city->getCorede();

        $form   = $this->getCityForm()->handleRequest($request)->createView();
        $cities = $this->getCities();

        $parameters = compact('form', 'options', 'cities', 'categoriesId',
            'corede');
        return $this->render('PROCERGSVPRCoreBundle:PollOption:viewByCity.html.twig',
                $parameters);
    }

    /**
     * @Route("/ballot", name="vpr_ballotByCity")
     * @Template()
     */
    public function viewByCityAction(Request $request)
    {
        $form = $this->getCityForm();

        $form->handleRequest($request);

        $options      = array();
        $categoriesId = array();

        if ($form->isValid()) {
            $em   = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $city = $data['city'];
            if ($city instanceof City) {
                $url = $this->generateUrl('vpr_ballot_view',
                    array('cityId' => $city->getName()));
                return $this->redirect($url);
            }
        }

        $form   = $form->createView();
        $cities = $this->getCities();

        return compact('form', 'options', 'cities', 'categoriesId', 'corede');
    }

    /**
     * @Route("/step/{stepId}/corede/{coredeId}", name="procergsvpr_step")
     * @Template()
     */
    public function stepAction(Request $request, $stepId, $coredeId)
    {
        $em            = $this->getDoctrine()->getManager();
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
        $poll       = $em->merge($vote->getBallotBox()->getPoll());
        $corede     = $em->merge($vote->getCorede());
        $step       = $em->merge($vote->getLastStep());

        $pollOptions = $repository->findByPollCoredeStep($poll, $corede, $step);

        $options      = array();
        $categoriesId = array();
        foreach ($pollOptions as $option) {
            $options[$option->getStep()->getName()][$option->getCategory()->getName()][]
                = $option;
            $categoriesId[$option->getCategory()->getName()]                             = $option->getCategory();
        }

        $form = $formBuilder->createView();
        return compact('form', 'corede', 'step', 'options', "categoriesId");
    }

    private function validateVotingSession(Request $request, $votingSession,
                                           $stepId, $coredeId)
    {
        $session = $request->getSession();
        $vote    = $votingSession->requireLastStep();

        if ($vote->getLastStep()->getId() != $stepId || $vote->getCorede()->getId()
            != $coredeId) {
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
        $stepRepo       = $em->getRepository('PROCERGSVPRCoreBundle:Step');
        $poll           = $em->merge($vote->getBallotBox()->getPoll());
        $corede         = $em->merge($vote->getCorede());
        $step           = $em->merge($vote->getLastStep());

        $vote->setCorede($corede);

        $options = $request->get('options');
        if (!$pollOptionRepo->checkStepOptions($step, $options)) {
            $session = $request->getSession();
            $session->getFlashBag()->add('danger',
                $this->get('translator')->trans('voting.session.invalid.options'));
            return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
        }
        $vote->addPollOption($options);
        $nextStep = $stepRepo->findNextPollStep($vote);
        if ($nextStep) {
            $vote->setLastStep($nextStep);
            $votingSession->updateVote($vote);
            $params = array(
                'stepId' => $vote->getLastStep()->getId(),
                'coredeId' => $vote->getCorede()->getId()
            );
            $url    = $this->generateUrl('procergsvpr_step', $params);
            return $this->redirect($url);
        }
        $votingSession->persistVote($vote, $this->getUser());

        $this->registerStats($em, $options, $corede, $poll, $vote);

        $em->flush();
        $vote->setLastStep(null);
        $vote->setPollOption(null);
        $votingSession->updateVote($vote);
        return $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
    }

    private function registerStats($em, $options, $corede, $poll, $vote)
    {
        $pollOptionRepo       = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $options              = $pollOptionRepo->getPollOption($vote);
        $hasLoginCidadao      = ($vote->getLoginCidadaoId()) ? true : false;
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

    private function getCityForm()
    {
        $em          = $this->getDoctrine()->getManager();
        $transformer = new CityToNameTransformer($em);
        $form        = $this->createFormBuilder()
            ->add('city', 'text',
                array(
                'required' => true,
                'label' => 'form.city.select',
                'invalid_message' => 'City not found.'
            ))
            ->add('submit', 'submit')
            ->setAction($this->generateUrl('vpr_ballotByCity'));
        $form->get('city')->addModelTransformer($transformer);

        return $form->getForm();
    }

    private function getCities()
    {
        $serializer = $this->container->get('jms_serializer');
        $em         = $this->getDoctrine()->getManager();
        $cityRepo   = $em->getRepository('PROCERGSVPRCoreBundle:City');
        return $serializer->serialize($cityRepo->findAll(), 'json',
                SerializationContext::create()->setSerializeNull(true)->setGroups(array(
                    'autocomplete'
        )));
    }
}
