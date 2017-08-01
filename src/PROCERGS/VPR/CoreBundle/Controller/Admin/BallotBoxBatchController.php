<?php
namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\BallotBoxBatch;
use PROCERGS\VPR\CoreBundle\Form\BallotBoxBatchType;
use PROCERGS\VPR\CoreBundle\Entity\BallotBoxBatchRepository;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/** 
 * @Route("/admin/ballotboxbatch")
 */
class BallotBoxBatchController extends Controller
{

    /**
     * @Route("/index", name="ballotboxbatch")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo BallotBoxBatchRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:BallotBoxBatch');
        $a = $request->get('poll_id');
        if (! $a) {
            $tempId = $this->get('session')->get('current_poll_id');
            if ($tempId) {
                return $this->redirect($this->generateUrl('ballotboxbatch', array(
                    'poll_id' => $tempId
                )));
            } else {
                return $this->redirect($this->generateUrl('ballotboxbatch', array(
                    'poll_id' => $pollRepo->findLastPoll()
                    ->getId()
                )));
            }
        } else {
            $currentPollId = $a;
            $this->get('session')->set('current_poll_id', $currentPollId);
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
     * @Route("/new", name="ballotboxbatch_new")
     * @Template("PROCERGSVPRCoreBundle:Admin\BallotBoxBatch:edit.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        $entity = new BallotBoxBatch();
        $entity->setPoll($pollRepo->find($request->get('poll_id')));
        if ($request->isMethod('POST')) {
            try {
                if (!isset($_FILES['lote']['tmp_name'])) {
                    throw new \Exception("Falta arquivo");
                }
                $entity->setCsvInputName(trim($_FILES['lote']['name']));
                $entity->setCsvInput(file_get_contents($_FILES['lote']['tmp_name']));
                $entity->setStatus1(BallotBoxBatch::STATUS_AGUARDA_PROCESSAMENTO);
                $openTime = $request->get('opening_time');
                if ($openTime) {
                    $openTime = \DateTime::createFromFormat("d/m/Y H:i:s",$openTime);
                    if (!$openTime) {
                        throw new \Exception("Hora de abertura invalida");
                    }
                    $entity->setOpeningTime($openTime);
                }
                $closeTime = $request->get('closing_time');
                if ($closeTime) {
                    $closeTime = \DateTime::createFromFormat("d/m/Y H:i:s",$closeTime);
                    if (!$closeTime) {
                        throw new \Exception("Hora de fechametno invalida");
                    }
                    $entity->setClosingTime($closeTime);
                }
                $em->persist($entity);
                $em->flush();
                $this->get('session')
                    ->getFlashBag()
                    ->add('success', 'Item cadastrado com sucesso');
                return $this->redirect($this->generateUrl('ballotboxbatch_edit', array(
                    'id' => $entity->getId()
                )));
            } catch (\Exception $e) {
                $this->get('session')
                    ->getFlashBag()
                    ->add('danger', $e->getMessage());
            }
        }
        if (!$entity->getOpeningTime()) {
            $entity->setOpeningTime($entity->getPoll()->getOpeningTime());
        }
        if (!$entity->getClosingTime()) {
            $entity->setClosingTime($entity->getPoll()->getClosingTime());
        }
        return array(
            'entity' => $entity
        );
    }

    /**
     * @Route("/{id}", name="ballotboxbatch_edit")
     * @Template("PROCERGSVPRCoreBundle:Admin\BallotBoxBatch:edit.html.twig")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBoxBatch')->find($id);
        if (! $entity) {
            $this->get('session')
                ->getFlashBag()
                ->add('danger', "Nao foi encontrado o item");
            return $this->redirect($this->generateUrl('ballotboxbatch'));
        }
        if ($request->isMethod('POST')) {
            try {
                //$entity->setName(trim($request->get('name')));
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
     * @Route("/{id}/delete", name="ballotboxbatch_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBoxBatch')->find($id);
        if (! $entity) {
            $this->get('session')
                ->getFlashBag()
                ->add('danger', "Nao foi encontrado o item");
            return $this->redirect($this->generateUrl('ballotboxbatch'));
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
        return $this->redirect($this->generateUrl('ballotboxbatch', array(
            'poll_id' => $entity->getPoll()->getId()
        )));
    }
    

    
    
}
