<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use PROCERGS\VPR\CoreBundle\Entity\Person;
use PROCERGS\VPR\CoreBundle\Entity\TREVoter;

class DefaultController extends Controller
{

    /**
     * @Template()https://drive.google.com/keep/
     */
    public function indexAction()
    {
        $voter = $this->get('security.context')->getToken()->getUser();
        return array('voter' => $voter);
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
        return $this->container->get('templating')->renderResponse('PROCERGSVPRCoreBundle:Registration:register.html.twig', array(
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

    public function ballotsAction(Request $request)
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
          $city = $cityRepo->findOneBy(array('name' => $data['city']));

          if ($city) {
            $pollRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll');

            $query = $pollRepo->createQueryBuilder('p')
                ->select('min(p.id) id') // TESTE - trocar para max !
                // ->select('max(p.id) id')
                ->getQuery();
            $poll = $query->getOneOrNullResult();

            $pollOptionsRepo = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');

            $query = $pollOptionsRepo->createQueryBuilder('p')
                ->Where('p.poll = :poll')
                ->andWhere('p.corede = :corede')
                ->setParameter('poll', $poll['id'])
                ->setParameter('corede', $city->getCorede())
                ->getQuery();
            $pollOptions = $query->getResult();


            foreach ($pollOptions as $option) {
              $step = $option->getStep()->getName();

              if ( !array_key_exists($step, $steps) ) {
                $steps[$step] = array();
              }
              array_push($steps[$step], $option);
            }
          }
        }

        $form = $form->createView();

        return $this->render('PROCERGSVPRCoreBundle:Default:ballots.html.twig', compact('form', 'steps'));
    }

}
