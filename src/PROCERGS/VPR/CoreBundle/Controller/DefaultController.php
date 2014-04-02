<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

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

    /**
     * @Template()
     */
    public function placesAction(Request $request)
    {
        $form = $this->createFormBuilder()
                     ->add('city', 'text', array(
                        'required' => true
                      ))
                     ->add('submit', 'submit')
                     ->getForm();

        $form->handleRequest($request);

        $boxes = array();
        if ($form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $data = $form->getData();

          $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
          $city = $cityRepo->findOneBy(array('name' => $data['city']));
          if ($city) {
            $repository = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:BallotBox');

            $query = $repository->createQueryBuilder('b')
                ->select('max(b.poll) poll')
                ->getQuery();
            $poll = $query->getOneOrNullResult();

            $query = $repository->createQueryBuilder('b')
                ->where('b.city = :city')
                ->andWhere('b.poll = :poll')
                ->setParameter('city', $city->getId())
                ->setParameter('poll', $poll)
                ->getQuery();
            $boxes = $query->getResult();


          } else {
            $form->addError(new FormError('not found city'));
          }

        }

        $form = $form->createView();


        return $this->render('PROCERGSVPRCoreBundle:Default:places.html.twig', compact('form', 'boxes'));
    }

}
