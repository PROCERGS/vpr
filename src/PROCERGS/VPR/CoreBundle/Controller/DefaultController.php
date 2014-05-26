<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use PROCERGS\VPR\CoreBundle\Entity\Person;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Exception\VotingTimeoutException;
use PROCERGS\VPR\CoreBundle\Exception\VoterAlreadyVotedException;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationAlreadyVotedException;
use PROCERGS\VPR\CoreBundle\Event\PersonEvent;
use PROCERGS\VPR\CoreBundle\Exception\TREVoterException;
use Symfony\Component\HttpFoundation\JsonResponse;
use PROCERGS\VPR\CoreBundle\Exception\RequestTimeoutException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Assetic\Exception\Exception;
use HWI\Bundle\OAuthBundle\Tests\Fixtures\OAuthAwareException;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="procergsvpr_core_homepage")
     * @Template()
     */
    public function indexAction()
    {
        $person = $this->getUser();
        $votingSession = $this->get('vpr_voting_session_provider');

        try {
            $vote = $votingSession->enforceVotingSession($person);
        } catch (VotingTimeoutException $e) {
            return $this->redirect($this->generateUrl('procergsvpr_core_voting_timeout'));
        } catch (VoterRegistrationAlreadyVotedException $e) {
            return $this->redirect($this->generateUrl('procergsvpr_core_voter_registration_voted'));
        } catch (VoterAlreadyVotedException $e) {
            return $this->redirect($this->generateUrl('fos_user_security_logout'));
        }

        $stepId = $votingSession->getNextStep();
        if (!$stepId) {
            if (!$vote->getVoterRegistration()) {
                return $this->redirect($this->generateUrl('procergsvpr_core_ask_voter_registration'));
            }
            if (!$vote->getNfgCpf()) {
                try {
                    $ball = $vote->getBallotBox();
                    $votingSession->checkExistingVotes($person, $ball);
                    $votingSession->flush();
                    return $this->indexAction();
                } catch (VoterRegistrationAlreadyVotedException $e) {
                    return $this->redirect($this->generateUrl('fos_user_security_logout',
                                            array('code' => $vote->getSmId())));
                } catch (VoterAlreadyVotedException $e) {
                    return $this->redirect($this->generateUrl('fos_user_security_logout',
                                            array('code' => $vote->getSmId())));
                }
            } else {
                return $this->redirect($this->generateUrl('fos_user_security_logout',
                                        array('code' => $vote->getSmId())));
            }
        }

        return $this->redirect($this->generateUrl('procergsvpr_step',
                                array(
                            'stepid' => $vote->getLastStep()->getId(),
                            'coredeid' => $vote->getCorede()->getid()
        )));
    }

    /**
     * @Route("/reinforce/doc", name="procergsvpr_core_ask_voter_registration")
     * @Template()
     */
    public function reinforceDocAction(Request $request)
    {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('trevoter', 'voter_registration',
                array(
            'required' => true
        ));
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        $messages = '';
        if ($form->isValid()) {
            $session = $request->getSession();
            $vote = $session->get('vote');
            if (!$vote || $vote->getLastStep()) {
                return $this->indexAction();
            }
            $user = $this->getUser();
            $dispatcher = $this->container->get('event_dispatcher');
            $treVoterTmp = $form->get('trevoter')->getData();
            $event = new PersonEvent($user, $treVoterTmp);
            try {
                $dispatcher->dispatch(PersonEvent::VOTER_REGISTRATION_EDIT,
                        $event);
                $em = $this->getDoctrine()->getManager();
                $vote = $em->merge($vote);
                $vote->setVoterRegistration($form->get('trevoter')->getData());
                $em->persist($vote);
                $em->flush();
                $session->set('vote', $vote);
                return $this->indexAction();
            } catch (TREVoterException $e) {
                $form->addError(new FormError($this->get('translator')->trans($e->getMessage())));
            }
        }
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforceDoc.html.twig',
                        array(
                    'form' => $form->createView(),
                    'messages' => $messages
        ));
    }

    /**
     * @Route("/end", name="procergsvpr_core_end")
     * @Template()
     */
    public function endAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('vote', null);
        return $this->render('PROCERGSVPRCoreBundle:Default:end.html.twig');
    }

    /**
     * @Route("/end/offer", name="procergsvpr_core_end_offer")
     * @Template()
     */
    public function endOfferAction(Request $request)
    {
        $r = $request->get('code');
        if (!$r) {
            $r = '';
        }
        $url['link_nfg'] = $this->container->getParameter('nfg_register_url');
        $url['link_lc'] = $this->container->getParameter('lc_register_url');
        $url['link_sm'] = $this->container->getParameter('sm_register_url') . $r;
        return $this->render('PROCERGSVPRCoreBundle:Default:endOffer.html.twig',
                        $url);
    }

    /**
     * @Route("/timeout", name="procergsvpr_core_voting_timeout")
     * @Template()
     */
    public function pollTimeoutAction()
    {
        $poll = $this->getDoctrine()->getManager()
                ->getRepository('PROCERGSVPRCoreBundle:Poll')
                ->findActivePoll();
        return array(
            'name' => $poll->getName(),
            'closingTime' => $poll->getClosingTime()
        );
    }

    /**
     * @Route("/end/change/offer", name="procergsvpr_core_voter_registration_voted")
     * @Template()
     */
    public function endChangeOfferAction()
    {
        $url['link_lc'] = $this->container->getParameter('lc_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:endChangeOffer.html.twig',
                        $url);
    }
    
    /**
     * @Route("/lc/poll", defaults={"_format" = "json"}, name="procergsvpr_core_end_lc_query")
     * @Template()
     */
    public function longPollingAction()
    {
        $user = $this->getUser();
        $accessToken = $user->getLoginCidadaoAccessToken();
        $url = $this->container->getParameter('login_cidadao_base_url');
        $now = new \DateTime();
        $url .= "/api/v1/person/wait/update?". http_build_query(array('access_token' => $accessToken, 'updated_at' => $now->format('Y-m-d H:i:s')));
        try {
            $person = $this->runTimeLimited(function() use ($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                if (!ini_get('open_basedir')) {
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                }
                curl_setopt($ch, CURLOPT_URL, $url);
                $response = curl_exec($ch);
                $info = curl_getinfo($ch);
                curl_close($ch);
                switch ($info['http_code']) {
                	case 200;
                    	$receivedPerson = json_decode($response, true);
                    	return ($response !== false && $receivedPerson) ? $receivedPerson : false;
                	break;
                	case 408:
                	    return false;
                	    break;
                	default:
                	    throw new \Exception($response, $info['http_code']);
                	    break;
                }
            });
        } catch(\Exception $e) {
            $received = $e->getMessage() ? json_decode($e->getMessage()) : null;
            return new JsonResponse(($received !== false && $received) ? $received : null, $e->getCode());
        }
        $return['full_name'] = strlen($person['full_name']) > 0;
        $return['email'] = $person['badges']['email'];
        $return['nfg_access_lvl'] = $person['badges']['nfg_access_lvl'] >= 2;
        $return['voter_registration'] = $person['badges']['voter_registration'];
        return new JsonResponse($return);
    }
    
    private function runTimeLimited($callback, $waitTime = 1)
    {        
        $limit = ini_get('max_execution_time') ? ini_get('max_execution_time') - 2 : 60;
        $startTime = time();
        while ($limit > 0) {
            $result = call_user_func($callback);
            $delta = time() - $startTime;
    
            if ($result !== false) {
                return $result;
            } else {
                $limit -= $delta;
                if ($limit <= 0) {
                    break;
                }
                $startTime = time();
                sleep($waitTime);
            }
        }
        throw new \Exception(json_encode(array('error' => 'Request Timeout')), '408');
    }    

}
