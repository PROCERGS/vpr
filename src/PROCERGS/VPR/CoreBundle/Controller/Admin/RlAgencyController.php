<?php
namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\RlAgency;
use PROCERGS\VPR\CoreBundle\Form\RlAgencyType;
use PROCERGS\VPR\CoreBundle\Entity\RlAgencyRepository;
use PROCERGS\VPR\CoreBundle\Entity\Poll;

/**
 * RlAgency controller.
 *
 * @Route("/rlagency")
 */
class RlAgencyController extends Controller
{

    /**
     * @Route("/index", name="rlagency")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo RlAgencyRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlAgency');
        $a = $request->get('poll_id');
        if (! $a) {
            return $this->redirect($this->generateUrl('rlagency', array(
                'poll_id' => $pollRepo->findLastPoll()
                    ->getId()
            )));
        } else {
            $currentPollId = $a;
        }
        $entities = $rlCriterioRepo->findEspecial1($currentPollId);
        $polls = $pollRepo->findBy(array(), array(
            'openingTime' => 'desc'
        ));
        return array(
            'entities' => $entities,
            'polls' => $polls,
            'currentPollId' => $currentPollId
        );
    }

    /**
     * @Route("/new", name="rlagency_new")
     * @Template("PROCERGSVPRCoreBundle:Admin\RlAgency:edit.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        $entity = new RlAgency();
        $entity->setPoll($pollRepo->find($request->get('poll_id')));        
        if ($request->isMethod('POST')) {
            try {
                $entity->setName(trim($request->get('name')));
                $em->persist($entity);
                $em->flush();
                $this->get('session')
                    ->getFlashBag()
                    ->add('success', 'Item cadastrado com sucesso');
                return $this->redirect($this->generateUrl('rlagency_edit', array(
                    'id' => $entity->getId()
                )));
            } catch (\Exception $e) {
                $this->get('session')
                    ->getFlashBag()
                    ->add('danger', $e->getMessage());
            }
        }
        return array(
            'entity' => $entity
        );
    }

    /**
     * @Route("/{id}", name="rlagency_edit")
     * @Template("PROCERGSVPRCoreBundle:Admin\RlAgency:edit.html.twig")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('PROCERGSVPRCoreBundle:RlAgency')->find($id);
        if (! $entity) {
            $this->get('session')
                ->getFlashBag()
                ->add('danger', "Nao foi encontrado o item");
            return $this->redirect($this->generateUrl('rlagency'));
        }
        if ($request->isMethod('POST')) {
            try {
                $entity->setName(trim($request->get('name')));
                $em->persist($entity);
                $em->flush();
                $this->get('session')
                    ->getFlashBag()
                    ->add('success', 'Item alterado com sucesso');
            } catch (\Exception $e) {
                $this->get('session')
                    ->getFlashBag()
                    ->add('danger', $e->getMessage());
            }
        }
        return array(
            'entity' => $entity
        );
    }

    /**
     * @Route("/{id}/delete", name="rlagency_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('PROCERGSVPRCoreBundle:RlAgency')->find($id);
        if (! $entity) {
            $this->get('session')
                ->getFlashBag()
                ->add('danger', "Nao foi encontrado o item");
            return $this->redirect($this->generateUrl('rlagency'));
        }
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')
                ->getFlashBag()
                ->add('success', 'Item excluido com sucesso');
        } catch (\Exception $e) {
            $this->get('session')
                ->getFlashBag()
                ->add('danger', $e->getMessage());
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('rlagency', array(
            'poll_id' => $entity->getPoll()->getId()
        )));
    }
}
