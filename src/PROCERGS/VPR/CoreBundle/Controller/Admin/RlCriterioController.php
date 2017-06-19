<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\RlCriterio;
use PROCERGS\VPR\CoreBundle\Form\RlCriterioType;
use PROCERGS\VPR\CoreBundle\Entity\PollRepository;
use PROCERGS\VPR\CoreBundle\Entity\RlCriterioRepository;

/**
 * RlCriterio controller.
 *
 * @Route("/admin/rlcriterio")
 */
class RlCriterioController extends Controller
{

    /**
     * Lists all RlCriterio entities.
     *
     * @Route("/index", name="rlcriterio")     
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo RlCriterioRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
        $a = $request->get('poll_id');
        if (!$a) {
            $tempId = $this->get('session')->get('current_poll_id');
            if ($tempId) {
                return $this->redirect($this->generateUrl('rlcriterio', array(
                    'poll_id' => $tempId
                )));
            } else {
                return $this->redirect($this->generateUrl('rlcriterio', array('poll_id' => $pollRepo->findLastPoll()->getId())));
            }
        } else {        
            $currentPollId = $a;
            $this->get('session')->set('current_poll_id', $currentPollId);
        }
        if ($request->isMethod('POST')) {
            $b = $request->get('item');
            try {
                $rlCriterioRepo->saveComplete($currentPollId, $b);
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
            }
            
        }
        $entities = $rlCriterioRepo->findEspecial1($currentPollId);
        $polls = $pollRepo->findBy(array(),array('openingTime' => 'desc'));
        return array(
            'entities' => $entities,
            'polls' => $polls,
            'currentPollId' => $currentPollId,
        );
    }
    
    /**
     * Lists poll stats.
     *
     * @Route("/export", name="rlcriterio_csv")
     */
    public function exportCsv(Request $request)
    {
        $currentPollId = $request->get('poll_id');
        if ($currentPollId) {
            $em = $this->getDoctrine()->getManager();
            /* @var $rlCriterioRepo RlCriterioRepository */
            $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
            $entities = $rlCriterioRepo->findEspecial1($currentPollId);
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="criterio_'.$currentPollId .'.csv";'
                );
            $response->sendHeaders();
            
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('corede_id', 'corede_name', 'tot_value', 'tot_program', 'program1', 'program2', 'program3', 'program4', 'program5'), $sep);
            foreach ($entities as $linha) {
                fputcsv($output, array($linha['corede_id'], utf8_decode($linha['corede_name']), $linha['tot_value'], $linha['tot_program'], $linha['program1'], $linha['program2'], $linha['program3'], $linha['program4'], $linha['program5']), $sep);
            }
            return $response;
        }
    }
    
}
