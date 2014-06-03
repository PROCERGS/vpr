<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionFilterType;
use PROCERGS\VPR\CoreBundle\Entity\StatsTotalCoredeVote;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\Response;

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

        $query = $em->createQueryBuilder()
            ->select('v')
            ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
            ->orderBy('v.totalVotes','DESC')
            ->getQuery();

        $results = $query->getResult();
        $entity = reset($results);
        $created_at = $entity->getCreatedAt();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($results, 'json');

        return array(
            'results' => $results,
            'jsonContent' => $jsonContent,
            'created_at' => $created_at
        );
    }

    /**
     * @Route("/stats", name="vpr_stats_main")
     * @Template()
     */
    public function votesMainAction()
    {
        return array();
    }

    /**
     * @Route("/stats/update_total_votes", name="vpr_stats_update_total_votes")
     */
    public function updateTotalVotesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $statsRepos = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');

        $results = $statsRepos->findTotalVotes();
        $created_at = new \DateTime();

        foreach($results as $line){
            $entity = $statsRepos->findOneByCoredeId($line['corede_id']);
            if(!$entity){
                $entity = new StatsTotalCoredeVote();
            }

            $entity->setBallotBoxId($line['ballot_box_id']);
            $entity->setCoredeId($line['corede_id']);
            $entity->setCoredeName($line['corede_name']);
            $entity->setTotalWithVoterRegistration($line['total_with_voter_registration']);
            $entity->setTotalWithLoginCidadao($line['total_with_login_cidadao']);
            $entity->setTotalVotes($line['total_votes']);
            $entity->setCreatedAt($created_at);

            $em->persist($entity);
            $em->flush();
        }

        $response = new JsonResponse();
        $response->setData(array(
            'success' => true,
            'created_at' => $created_at
        ));

        return $response;
    }

    /**
     * @Route("/stats/votes_by_corede", name="vpr_stats_votes_by_corede")
     */
    public function votesByCoredeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('v')
            ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
            ->orderBy('v.totalVotes','DESC')
            ->getQuery();

        $results = $query->getResult();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($results, 'json');

        $response = new Response($jsonContent);
        $response->headers->add(array('Content-Type'=> 'application/json'));
        return $response;
    }
}