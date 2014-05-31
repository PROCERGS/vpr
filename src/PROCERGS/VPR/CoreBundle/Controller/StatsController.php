<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionFilterType;

class StatsController extends Controller
{

    /**
     * @Route("/stats/option_vote_by_corede", name="vpr_option_vote_by_corede")
     * @Template()
     */
    public function optionVoteByCoredeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $statsRepos = $em->getRepository('PROCERGSVPRCoreBundle:StatsOptionVote');

        $form = $this->createForm(new PollOptionFilterType());

        $results = array();
        if ($request->isMethod('POST')) {
            $form->bind($request);

            $poll = $form->get('poll')->getData();
            $corede = $form->get('corede')->getData();

            $steps = $poll->getSteps();
            foreach($steps as $step){
                //$results[$step->getName()] = $statsRepos->findPercentOptionVoteByCorede($corede->getId(),$step->getId());
                $results[$step->getName()] = $statsRepos->findOptionVoteByCorede($corede->getId(),$step->getId());
            }
        }

        return array(
            'form' => $form->createView(),
            'results' => $results
        );
    }

    /**
     * @Route("/stats/votes", name="vpr_stats_votes")
     * @Template()
     */
    public function votesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $statsRepos = $em->getRepository('PROCERGSVPRCoreBundle:StatsOptionVote');

        $results = $statsRepos->findTotalVotes();
        $created_at = date('d/m/Y H:i:s');

        return array(
            'results' => $results,
            'created_at' => $created_at
        );
    }
}