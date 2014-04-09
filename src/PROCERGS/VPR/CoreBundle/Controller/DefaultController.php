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

class DefaultController extends Controller
{

    /**
     * @Template()
     */
    public function indexAction()
    {
        $voter = $this->get('security.context')
            ->getToken()
            ->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $session = $this->getRequest()->getSession();
        $vote = $session->get('vote');
        if (! $vote) {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findActivePoll();
            if (false && $poll->getClosingTime() < new \DateTime()) {
                return $this->render('PROCERGSVPRCoreBundle:Default:endTimeOverAction.html.twig', array(
                    'name' => $poll->getName(),
                    'closingTime' => $poll->getClosingTime()
                ));
            }
            
            $ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->findBy(array(
                'poll' => $poll,
                'isOnline' => 1
            ));
            $filter = array(
                'ballotBox' => $ballotBox[0]
            );
            if ($voter->getTreVoter()) {
                $filter['voterRegistration'] = $voter->getTreVoter()->getId();
            }
            if ($voter->getLoginCidadaoId()) {
                $filter['loginCidadaoId'] = $voter->getLoginCidadaoId();
            }
            $vote1 = $em->getRepository('PROCERGSVPRCoreBundle:Vote')->findBy($filter);
            if ($vote1) {
                foreach ($vote1 as $try) {
                    if ($try->getNfgCpf()) {
                        return $this->redirect($this->generateUrl('procergsvpr_core_end'));
                    }                    
                }
                return $this->redirect($this->generateUrl('procergsvpr_core_tituloVotou'));
            }
            $vote = $this->_createVote($em, $session, $voter, $ballotBox[0]);
            $em->detach($vote);
            $session->set('vote', $vote);
        } else {
            if (! $vote->getLastStep()) {
                if (! $vote->getVoterRegistration()) {
                    return $this->redirect($this->generateUrl('procergsvpr_core_endvote'));
                }
                $filter['ballotBox'] = $vote->getBallotBox();
                $filter['voterRegistration'] = $vote->getVoterRegistration();
                if ($vote->getLoginCidadaoId()) {
                    $filter['loginCidadaoId'] = $vote->getLoginCidadaoId();
                }
                if (! $vote->getNfgCpf()) {
                    $vote1 = $em->getRepository('PROCERGSVPRCoreBundle:Vote')->findBy($filter);
                    if ($vote1) {
                        foreach ($vote1 as $try) {
                            if ($try->getNfgCpf()) {
                                return $this->redirect($this->generateUrl('procergsvpr_core_end'));
                            }
                        }
                        return $this->redirect($this->generateUrl('procergsvpr_core_sem_nfg'));
                    } else {
                        $session->set('vote', null);
                        return $this->indexAction();
                    }
                } else {
                    return $this->redirect($this->generateUrl('procergsvpr_core_end'));
                }
            }
        }
        return $this->redirect($this->generateUrl('procergsvpr_step', array(
            'id' => $vote->getLastStep()
                ->getId()
        )));
    }

    public function reinforceOfferAction()
    {
        $nfgRegisterUrl = $this->container->getParameter('nfg_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforceOffer.html.twig', array(
            'nfgRegisterUrl' => $nfgRegisterUrl
        ));
    }

    public function reinforceAction(Request $request)
    {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('cpf', 'text', array(
            'required' => true
        ));
        $formBuilder->add('birthdate', 'birthday', array(
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
            if (! $vote || $vote->getLastStep()) {
                return $this->indexAction();
            }
            $voter = $this->get('security.context')
                ->getToken()
                ->getUser();
            $nfgWs = $this->get('procergs.nfgws');
            $nfgWs->setTituloEleitor($vote->getVoterRegistration());
            $nfgWs->setCpf($form->get('cpf')
                ->getData());
            $nfgWs->setDataNascimento($form->get('birthdate')
                ->getData());
            $nfgWs->setNome($voter->getFirstName() . ' ' . $voter->getSurname());
            if (false) {
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
                        $form->addError(new FormError('posvote.reinforce.nfg.user.wrongdata'));
                        break;
                    case 9:
                        $form->addError(new FormError('posvote.reinforce.nfg.user.notfound'));
                        break;
                    default:
                        $form->addError(new FormError('posvote.reinforce.nfg.return.problem'));
                        break;
                }
            } else {
                $form->addError(new FormError('posvote.reinforce.nfg.query.problem'));
            }
        }
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforce.html.twig', array(
            'form' => $form->createView(),
            'messages' => $messages
        ));
    }

    public function reinforcePunyAction()
    {
        $nfgRegisterUrl = $this->container->getParameter('nfg_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:reinforcePuny.html.twig', array(
            'nfgRegisterUrl' => $nfgRegisterUrl
        ));
    }

    public function endAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('vote', null);
        return $this->render('PROCERGSVPRCoreBundle:Default:end.html.twig');
    }

    public function endTimeOverAction()
    {
        return $this->render('PROCERGSVPRCoreBundle:Default:endTimeOver.html.twig');
    }

    public function endChangePunyAction()
    {
        $nfgRegisterUrl = $this->container->getParameter('nfg_register_url');
        return $this->render('PROCERGSVPRCoreBundle:Default:endChangePuny.html.twig', array(
            'nfgRegisterUrl' => $nfgRegisterUrl
        ));
    }

    public function endChangeAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $vote = $session->get('vote');
        if ($vote) {
            return $this->indexAction();
        }
        $voter = $this->get('security.context')
            ->getToken()
            ->getUser();
        $chan = new \stdClass();
        $chan->trevote = null;
        $chan->cpf = null;
        $chan->birthdate = null;
        if ($voter->getTreVoter()) {
            $chan->trevoter = $voter->getTreVoter()->getId();
        }
        
        $formBuilder = $this->createFormBuilder($chan);
        $formBuilder->add('cpf', 'text', array(
            'required' => true
        ));
        $formBuilder->add('birthdate', 'birthday', array(
            'required' => true,
            'format' => 'dd MMMM yyyy',
            'widget' => 'choice',
            'years' => range(date('Y'), date('Y') - 70)
        ));
        $formBuilder->add('trevoter', 'text', array(
            'required' => true,
            'max_length' => 12
        ));
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        $messages = '';
        if ($form->isValid()) {
            $nfgWs = $this->get('procergs.nfgws');
            $nfgWs->setTituloEleitor($form->get('trevoter')
                ->getData());
            $nfgWs->setCpf($form->get('cpf')
                ->getData());
            $nfgWs->setDataNascimento($form->get('birthdate')
                ->getData()
                ->format('Y-m-d'));
            $nfgWs->setNome($voter->getFirstName() . ' ' . $voter->getSurname());
            if (false) {
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
                            $ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->findBy(array(
                                'poll' => $poll,
                                'isOnline' => 1
                            ));
                            $vote = $this->_createVote($em, $session, $voter, $ballotBox[0]);
                            $vote->setNfgCpf('1');
                            $em->detach($vote);
                            $session->set('vote', $vote);
                            return $this->indexAction();
                        } else {
                            return $this->redirect($this->generateUrl('procergsvpr_core_end_change_puny'));
                        }
                        return $this->indexAction();
                    case 2:
                        $form->addError(new FormError('end.change.nfg.user.wrongdata'));
                        break;
                    case 9:
                        $form->addError(new FormError('end.change.nfg.user.notfound'));
                        break;
                    default:
                        $form->addError(new FormError('end.change.nfg.return.problem'));
                        break;
                }
            } else {
                $form->addError(new FormError('end.change.nfg.query.problem'));
            }
        }
        return $this->render('PROCERGSVPRCoreBundle:Default:endChange.html.twig', array(
            'form' => $form->createView(),
            'messages' => $messages
        ));
    }

    /**
     * @Template()
     */
    public function municipioAction()
    {
        $voter = $this->get('security.context')
            ->getToken()
            ->getUser();
        return array(
            'voter' => $voter
        );
    }

    public function registerAction(Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        
        $user = new Person();
        $form = $formFactory->createForm();
        $form->setData($user);
        
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $new = new TREVoter();
                $new->setId($form->get('username')
                    ->getData());
                $user1 = $userManager->findUserBy(array(
                    'treVoter' => $new
                ));
                if ($user1) {
                    if ($this->_testName($form->get('firstname')
                        ->getData(), $user1->getFirstName())) {
                        $response = $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
                        $this->_auth($user1, $response);
                        return $response;
                    } else {
                        $form->addError(new FormError('register.voter_registration.mismatch'));
                    }
                } else {
                    $em = $this->getDoctrine()->getManager();
                    $treRepo = $em->getRepository('PROCERGSVPRCoreBundle:TREVoter');
                    $voter = $treRepo->findOneBy(array(
                        'id' => $form->get('username')
                            ->getData()
                    ));
                    if ($voter) {
                        if ($this->_testName($voter->getName(), $user->getFirstName())) {
                            $user->setTreVoter($voter);
                            $user->setCity($voter->getCity());
                            $userManager->updateUser($user);
                            $response = $this->redirect($this->generateUrl('procergsvpr_core_homepage'));
                            $this->_auth($user, $response);
                            return $response;
                        } else {
                            $form->addError(new FormError('register.voter_registration.mismatch'));
                        }
                    } else {
                        $form->addError(new FormError('register.voter_registration.notfound'));
                    }
                }
            }
        }
        return $this->render('PROCERGSVPRCoreBundle:Registration:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function _testName($val1, $val2)
    {
        $a1 = explode(' ', $val1);
        $a2 = explode(' ', $val2);
        return (mb_strtolower(trim($a1[0])) === mb_strtolower(trim($a2[0])));
    }

    private function _auth($user, $response)
    {
        try {
            $loginManager = $this->container->get('fos_user.security.login_manager');
            $firewallName = $this->container->getParameter('fos_user.firewall_name');
            $loginManager->loginUser($firewallName, $user, $response);
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }

    private function _createVote($em, $session, $voter, $ballotBox)
    {
        $vote = new Vote();
        $vote->setAuthType($voter->getLoginCidadaoAccessToken() ? Vote::AUTH_LOGIN_CIDADAO : Vote::AUTH_VOTER_REGISTRATION);
        $vote->setBallotBox($ballotBox);
        $vote->setCorede($voter->getCity()
            ->getCorede());
        if ($voter->getTreVoter()) {
            $vote->setVoterRegistration($voter->getTreVoter()
                ->getId());
        }
        if ($voter->getLoginCidadaoId()) {
            $vote->setLoginCidadaoId($voter->getLoginCidadaoId());
        }
        $pollOptionRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $vote->setLastStep($pollOptionRepo->getNextPollStep($vote));
        return $vote;
    }
}
