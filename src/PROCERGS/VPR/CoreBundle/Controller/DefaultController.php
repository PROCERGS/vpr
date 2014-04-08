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
                return $this->render('PROCERGSVPRCoreBundle:Default:horarioEncerrado.html.twig', array(
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
                if ($vote1->getNfgCpf()) {
                    die('go to nfg');
                } else {
                    return $this->redirect($this->generateUrl('procergsvpr_core_end'));
                }
            }
            $vote = new Vote();
            $vote->setBallotBox($ballotBox[0]);
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
                $vote1 = $em->getRepository('PROCERGSVPRCoreBundle:Vote')->findBy($filter);
                if ($vote1) {
                    if (! $vote1->getNfgCpf()) {
                        return $this->redirect($this->generateUrl('procergsvpr_core_sem_nfg'));
                    } else {
                        return $this->redirect($this->generateUrl('procergsvpr_core_end'));
                    }
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
            if (! $vote) {
                return 'do something mutley';
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
            // $return = $nfgWs->consultaCadastro();
            $return['CodSitRetorno'] = 1;
            if (is_numeric($return['CodSitRetorno'])) {
                switch ($return['CodSitRetorno']) {
                    case 1:                        
                        return $this->redirect($this->generateUrl('procergsvpr_core_end'));
                        break;
                    case 2:
                        return $this->redirect($this->generateUrl('procergsvpr_core_end'));
                        break;
                    case 3:
                        return $this->redirect($this->generateUrl('procergsvpr_core_end'));
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

    public function endAction()
    {
        return $this->render('PROCERGSVPRCoreBundle:Default:end.html.twig');
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
}
