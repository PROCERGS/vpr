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
     * @Template()https://drive.google.com/keep/
     */
    public function indexAction()
    {
        $voter = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();        
        
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findActivePoll();
        if (false && $poll->getClosingTime() < new \DateTime()) {
            return $this->render('PROCERGSVPRCoreBundle:Default:horarioEncerrado.html.twig', array(
                'name' => $poll->getName(), 
                'closingTime' => $poll->getClosingTime()
            ));            
        }
                
        $session = $this->getRequest()->getSession();
        $vote = $session->get('vote');
        if (!$vote) {
            $ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->findBy(array('poll' => $poll, 'isOnline' => 1));
            $filter = array('ballotBox' => $ballotBox[0]);
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
                    die('already voted');
                }
            }
            $vote = new Vote();
            $vote->setBallotBox($ballotBox[0]);
            $vote->setCorede($voter->getCity()->getCorede());
            if ($voter->getTreVoter()) {
                $vote->setVoterRegistration($voter->getTreVoter()->getId());
            }
            if ($voter->getLoginCidadaoId()) {
                $vote->setLoginCidadaoId($voter->getLoginCidadaoId());
            }
            $pollOptionRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');                        
            $vote->setLastStep($pollOptionRepo->getNextPollStep($vote));
            $em->detach($vote);
            $session->set('vote', $vote);
        }
        return $this->redirect($this->generateUrl('procergsvpr_step', array('id' => $vote->getLastStep()->getId())));
    }
    
    public function stepAction($id)
    {
        
    }

    /**
     * @Template()
     */
    public function municipioAction()
    {
        $voter = $this->get('security.context')->getToken()->getUser();
        return array('voter' => $voter);
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
                    if ($this->_testName($form->get('firstname')->getData(), $user1->getFirstName())) {
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
