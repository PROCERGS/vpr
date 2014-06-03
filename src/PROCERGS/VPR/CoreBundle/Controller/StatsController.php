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
use PROCERGS\VPR\CoreBundle\Helper\Utils;

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
            $entity->setTotalWithVoterRegistrationAndLoginCidadao($line['total_with_voter_registration_and_login_cidadao']);
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
    
    /**
     * @Route("/stats/graphics", name="vpr_stats_graphics")
     * @Template()
     */
    public function graphicsAction()
    {
        return array();
    }
    
    /**
     * @Route("/stats/graphics/query1", name="vpr_stats_graphics_query1")
     * @Template()
     */
    public function query1Action() {
        $em = $this->getDoctrine()->getManager();
        $statsRepos = $em->getRepository('PROCERGSVPRCoreBundle:StatsOptionVote');
        $items = $statsRepos->query1();
    
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQueryBuilder()
        ->select('v')
        ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
        ->orderBy('v.totalVotes','DESC')
        ->getQuery();
        
        $results = $query->getResult();
        
        $maxQuantity = 0;
        $maxAmount = 0;
        foreach ($items as $idx => $item) {
            $items[$idx]['quantity'] = 0;
            $items[$idx]['quantityReg'] = 0;
            $items[$idx]['quantityLc'] = 0;
            foreach ($results as $val) {
                if ($val->getCoredeId() == $item['codcorede']) {
                    $items[$idx]['quantity'] = $val->getTotalVotes();
                    $maxAmount += $val->getTotalVotes();
                    if ($val->getTotalVotes() > $maxQuantity) {
                        $maxQuantity = $val->getTotalVotes();
                    }
                    $items[$idx]['quantityReg'] = $val->getTotalWithVoterRegistration();
                    $items[$idx]['quantityLc'] = $val->getTotalWithLoginCidadao();
                }
            }
        }
        foreach($items as $item) {
            $obj[] = array(
                "color"         => Utils::colorByQuantity($item["quantity"], $maxQuantity),
                "size"          => Utils::sizeByAmount($item["quantity"], $maxAmount),
                "quantity"      => $item["quantity"],                
                "population"    => $item["populacao"],
                "lat"           => $item["latitude"],
                "long"          => $item["longitude"],
                "name"          => $item["corede"],
                "link_corede"   => null,
                "quantityReg"  => $item["quantityReg"],
                "quantityLc"  => $item["quantityLc"],
            );
        }
        return new JsonResponse($obj);
    }
    
}