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
use PROCERGS\VPR\CoreBundle\Entity\PollOption;
use PROCERGS\VPR\CoreBundle\Exception\VotingTimeoutException;
use PROCERGS\VPR\CoreBundle\Exception\VoterAlreadyVotedException;
use PROCERGS\VPR\CoreBundle\Exception\VoterRegistrationAlreadyVotedException;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{

    /**
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
            if (! $vote->getNfgCpf()) {
                try {
                    $ball = $vote->getBallotBox();
                    $votingSession->checkExistingVotes($person, $ball);
                    $votingSession->flush();
                    return $this->indexAction();
                } catch (VoterRegistrationAlreadyVotedException $e) {
                    return $this->redirect($this->generateUrl('fos_user_security_logout', array('code' => $vote->getSmId())));
                } catch (VoterAlreadyVotedException $e) {
                    return $this->redirect($this->generateUrl('fos_user_security_logout', array('code' => $vote->getSmId())));
                }
            } else {
                return $this->redirect($this->generateUrl('fos_user_security_logout', array('code' => $vote->getSmId())));
            }
        }

        return $this->redirect($this->generateUrl('procergsvpr_step',
                                array(
                            'stepid' => $vote->getLastStep()->getId(),
                            'coredeid' => $vote->getCorede()->getid()
        )));
    }

    public function reinforceOfferAction()
    {
        $nfgRegisterUrl = $this->container->getParameter('nfg_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforceOffer.html.twig',
                        array(
                    'nfgRegisterUrl' => $nfgRegisterUrl
        ));
    }

    public function reinforceAction(Request $request)
    {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('cpf', 'text',
                array(
            'required' => true
        ));
        $formBuilder->add('birthdate', 'birthday',
                array(
            'required' => true,
            'format' => 'dd MMMM yyyy',
            'widget' => 'choice',
            'years' => range(date('Y'), date('Y') - 70)
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
            $voter = $this->get('security.context')->getToken()->getUser();
            $nfgWs = $this->get('procergs.nfgws');
            $nfgWs->setTituloEleitor($vote->getVoterRegistration());
            $nfgWs->setCpf($form->get('cpf')->getData());
            $nfgWs->setDataNascimento($form->get('birthdate')->getData());
            $nfgWs->setNome($voter->getFirstName() . ' ' . $voter->getSurname());
            if (!$this->container->getParameter('nfg_ws_rand')) {
                $return = $nfgWs->consultaCadastro();
            } else {
                $return['CodSitRetorno'] = mt_rand(1, 4);
                $return['CodNivelAcesso'] = mt_rand(1, 2);
            }
            if (is_numeric($return['CodSitRetorno'])) {
                switch ($return['CodSitRetorno']) {
                    case 1:
                        if ($return['CodNivelAcesso'] >= 2) {
                            $em = $this->getDoctrine()->getManager();
                            $vote = $em->merge($vote);
                            $vote->setNfgCpf('1');
                            $em->persist($vote);
                            $em->flush();
                            $session->set('vote', $vote);
                            return $this->indexAction();
                        } else {
                            return $this->redirect($this->generateUrl('procergsvpr_core_nfgFraca'));
                        }
                        return $this->indexAction();
                    case 2:
                        $form->addError(new FormError($this->get('translator')->trans('posvote.reinforce.nfg.user.wrongdata')));
                        break;
                    case 9:
                        $form->addError(new FormError($this->get('translator')->trans('posvote.reinforce.nfg.user.notfound')));
                        break;
                    default:
                        $form->addError(new FormError($this->get('translator')->trans('posvote.reinforce.nfg.return.problem')));
                        break;
                }
            } else {
                $form->addError(new FormError($this->get('translator')->trans('posvote.reinforce.nfg.query.problem')));
            }
        }
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforce.html.twig',
                        array(
                    'form' => $form->createView(),
                    'messages' => $messages
        ));
    }

    public function reinforcePunyAction()
    {
        $nfgRegisterUrl = $this->container->getParameter('nfg_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforcePuny.html.twig',
                        array(
                    'nfgRegisterUrl' => $nfgRegisterUrl
        ));
    }

    public function reinforceDocAction(Request $request)
    {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('trevoter', 'voter_registration',
                array(
            'required' => true,
            'max_length' => 12
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
            $voter = $this->get('security.context')->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();
            $treRepo = $em->getRepository('PROCERGSVPRCoreBundle:TREVoter');
            $voter1 = $treRepo->findOneBy(array(
                'id' => $form->get('trevoter')->getData()
            ));
            if ($voter1) {
                if ($this->_testName($voter1->getName(), $voter->getFirstName())) {
                    $vote = $em->merge($vote);
                    $vote->setVoterRegistration($form->get('trevoter')->getData());
                    $em->persist($vote);
                    $em->flush();
                    $session->set('vote', $vote);
                    return $this->indexAction();
                } else {
                    $form->addError(new FormError($this->get('translator')->trans('register.voter_registration.mismatch')));
                }
            } else {
                $form->addError(new FormError($this->get('translator')->trans('register.voter_registration.notfound')));
            }
        }
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforceDoc.html.twig',
                        array(
                    'form' => $form->createView(),
                    'messages' => $messages
        ));
    }

    public function endAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('vote', null);
        return $this->render('PROCERGSVPRCoreBundle:Default:end.html.twig');
    }

    public function endOfferAction(Request $request)
    {
        $r = $request->get('code');
        if (!$r) {
            $r = '';
        }
        $url['link_nfg'] = $this->container->getParameter('nfg_register_url');
        $url['link_lc'] = $this->container->getParameter('lc_register_url');
        $url['link_sm'] = $this->container->getParameter('sm_register_url').$r;
        return $this->render('PROCERGSVPRCoreBundle:Default:endOffer.html.twig', $url);
    }

    public function endTimeOverAction($poll = null)
    {
        $poll = $this->getDoctrine()->getManager()->getRepository('PROCERGSVPRCoreBundle:Poll')->findActivePoll();
        return $this->render('PROCERGSVPRCoreBundle:Default:endTimeOver.html.twig',
                        array(
                    'name' => $poll->getName(),
                    'closingTime' => $poll->getClosingTime()
        ));
    }

    public function endChangePunyAction()
    {
        $nfgRegisterUrl = $this->container->getParameter('nfg_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:endChangePuny.html.twig',
                        array(
                    'nfgRegisterUrl' => $nfgRegisterUrl
        ));
    }

    public function endChangeOfferAction()
    {
        $url['link_lc'] = $this->container->getParameter('lc_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:endChangeOffer.html.twig', $url);
    }

    public function endChangeAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $vote = $session->get('vote');
        if ($vote) {
            return $this->indexAction();
        }
        $voter = $this->get('security.context')->getToken()->getUser();
        $chan = new \stdClass();
        $chan->trevote = null;
        $chan->cpf = null;
        $chan->birthdate = null;
        if ($voter->getTreVoter()) {
            $chan->trevoter = $voter->getTreVoter()->getId();
        }

        $formBuilder = $this->createFormBuilder($chan);
        $formBuilder->add('cpf', 'text',
                array(
            'required' => true
        ));
        $formBuilder->add('birthdate', 'birthday',
                array(
            'required' => true,
            'format' => 'dd MMMM yyyy',
            'widget' => 'choice',
            'years' => range(date('Y'), date('Y') - 70)
        ));
        $formBuilder->add('trevoter', 'voter_registration');
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        $messages = '';
        if ($form->isValid()) {
            $nfgWs = $this->get('procergs.nfgws');
            $nfgWs->setTituloEleitor($form->get('trevoter')->getData());
            $nfgWs->setCpf($form->get('cpf')->getData());
            $nfgWs->setDataNascimento($form->get('birthdate')->getData()->format('Y-m-d'));
            $nfgWs->setNome($voter->getFirstName() . ' ' . $voter->getSurname());
            if (!$this->container->getParameter('nfg_ws_rand')) {
                $return = $nfgWs->consultaCadastro();
            } else {
                $return['CodSitRetorno'] = mt_rand(1, 4);
                $return['CodNivelAcesso'] = mt_rand(1, 2);
            }
            if (is_numeric($return['CodSitRetorno'])) {
                switch ($return['CodSitRetorno']) {
                    case 1:
                        if ($return['CodNivelAcesso'] >= 2) {
                            $em = $this->getDoctrine()->getManager();
                            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findActivePoll();
                            $ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->findOnlineByPoll($poll);
                            $vote = $this->_createVote($em, $session, $voter,
                                    $ballotBox);
                            $vote->setNfgCpf('1');
                            $em->detach($vote);
                            $session->set('vote', $vote);
                            return $this->indexAction();
                        } else {
                            return $this->redirect($this->generateUrl('procergsvpr_core_end_change_puny'));
                        }
                        return $this->indexAction();
                    case 2:
                        $form->addError(new FormError($this->get('translator')->trans('end.change.nfg.user.wrongdata')));
                        break;
                    case 9:
                        $form->addError(new FormError($this->get('translator')->trans('end.change.nfg.user.notfound')));
                        break;
                    default:
                        $form->addError(new FormError($this->get('translator')->trans('end.change.nfg.return.problem')));
                        break;
                }
            } else {
                $form->addError(new FormError($this->get('translator')->trans('end.change.nfg.query.problem')));
            }
        }
        return $this->render('PROCERGSVPRCoreBundle:Default:endChange.html.twig',
                        array(
                    'form' => $form->createView(),
                    'messages' => $messages
        ));
    }

    /**
     * @Template()
     */
    public function municipioAction()
    {
        $voter = $this->get('security.context')->getToken()->getUser();
        return array(
            'voter' => $voter
        );
    }

    /**
     * @deprecated since version 1.0
     * @param type $val1
     * @param type $val2
     * @return type
     */
    private function _testName($val1, $val2)
    {
        $a1 = explode(' ', $val1);
        $a2 = explode(' ', $val2);
        return (mb_strtolower(trim($a1[0])) === mb_strtolower(trim($a2[0])));
    }

    /**
     * @deprecated since version 1.0
     * @param type $user
     * @param type $response
     */
    private function _auth($user, $response)
    {
        try {
            $loginManager = $this->get('fos_user.security.login_manager');
            $firewallName = $this->getParameter('fos_user.firewall_name');
            $loginManager->loginUser($firewallName, $user, $response);
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }

    /**
     * @deprecated since version 1.0
     * @param type $em
     * @param type $session
     * @param type $voter
     * @param type $ballotBox
     * @return \PROCERGS\VPR\CoreBundle\Entity\Vote
     */
    private function _createVote($em, $session, $voter, $ballotBox)
    {
        $vote = new Vote();
        $vote->setAuthType($voter->getLoginCidadaoAccessToken() ? Vote::AUTH_LOGIN_CIDADAO : Vote::AUTH_VOTER_REGISTRATION);
        $vote->setBallotBox($ballotBox);
        $vote->setCorede($voter->getCity()->getCorede());
        if ($voter->getTreVoter()) {
            $vote->setVoterRegistration($voter->getTreVoter()->getId());
        }
        if ($voter->getLoginCidadaoId()) {
            $vote->setLoginCidadaoId($voter->getLoginCidadaoId());
        }
        $pollOptionRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $vote->setLastStep($pollOptionRepo->getNextPollStep($vote));
        return $vote;
    }

}
