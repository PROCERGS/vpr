<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\RlCriterioMun;
use PROCERGS\VPR\CoreBundle\Form\RlCriterioMunType;
use PROCERGS\VPR\CoreBundle\Entity\RlCriterioMunRepository;

/**
 * RlCriterioMun controller.
 *
 * @Route("/admin/rlcriteriomun")
 */
class RlCriterioMunController extends Controller
{

    /**
     * Lists all RlCriterioMun entities.
     *
     * @Route("/index", name="rlcriteriomun")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo RlCriterioMunRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterioMun');
        $a = $request->get('poll_id');
        if (!$a) {
            $tempId = $this->get('session')->get('current_poll_id');
            if ($tempId) {
                return $this->redirect($this->generateUrl('rlcriteriomun', array(
                    'poll_id' => $tempId
                )));
            } else {
                return $this->redirect($this->generateUrl('rlcriteriomun', array('poll_id' => $pollRepo->findLastPoll()->getId())));
            }
            
        } else {
            $currentPollId = $a;
            $this->get('session')->set('current_poll_id', $currentPollId);
        }        
        if ($request->isMethod('POST')) {
            $b = $request->get('item1');
            $c = $request->get('item2');
            try {
                $rlCriterioRepo->saveComplete($currentPollId, $b, $c);
            } catch (\Exception $e) {
                if (stristr($e->getMessage(), 'uk_rl_criterio_mun1') !== false) {
                    $msg = "Não pode repetir valores";                    
                } else {
                    $msg = $e->getMessage();
                }
                $this->get('session')->getFlashBag()->add('danger', $msg);
            }
        }
        $entities1 = $rlCriterioRepo->findEspecial1($currentPollId, RlCriterioMun::CALC_POPULATION);
        $entities2 = $rlCriterioRepo->findEspecial1($currentPollId, RlCriterioMun::CALC_PROGRAM);
        $polls = $pollRepo->findBy(array(),array('openingTime' => 'desc'));
        return array(
            'entities1' => $entities1,
            'entities2' => $entities2,
            'polls' => $polls,
            'currentPollId' => $currentPollId,
        );
    }
    
    /**
     * Lists poll stats.
     *
     * @Route("/export", name="rlcriteriomun_csv")
     */
    public function exportCsv(Request $request)
    {
        $currentPollId = $request->get('poll_id');
        $typeCalc = $request->get('type_calc');
        if ($currentPollId && $typeCalc) {
            $em = $this->getDoctrine()->getManager();
            /* @var $rlCriterioRepo RlCriterioMunRepository */
            $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterioMun');
            $entities = $rlCriterioRepo->findEspecial1($currentPollId, $typeCalc);
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="criterio_'.$currentPollId .'_'.$typeCalc.'.csv";'
                );
            $response->sendHeaders();
    
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('type_calc', 'limit_citizen', 'perc_apply'), $sep);
            foreach ($entities as $linha) {
                fputcsv($output, array($linha['type_calc'], $linha['limit_citizen'], $linha['perc_apply']), $sep);
            }
            return $response;
        }
    }
    
}
