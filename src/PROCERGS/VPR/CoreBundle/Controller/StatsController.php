<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionFilterType;
use PROCERGS\VPR\CoreBundle\Entity\StatsTotalCoredeVote;
use PROCERGS\VPR\CoreBundle\Entity\StatsTotalOptionVote;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\Response;
use PROCERGS\VPR\CoreBundle\Helper\Utils;

class StatsController extends Controller
{

    /**
     * @Route("/reports/option/votes", name="vpr_option_vote_by_corede")
     * @Template()
     */
    public function optionVotesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $statsRepos = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalOptionVote');

        $form = $this->createForm(new PollOptionFilterType());
        
        $results = array();
        $created_at = null;
        $statsCorede = null;
        if ($request->isMethod('POST')) {
            $form->bind($request);

            $poll = $form->get('poll')->getData();
            $corede = $form->get('corede')->getData();

            $statsCorede = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote')->findOneByCoredeId($corede->getId());

            $steps = $poll->getSteps();
            foreach($steps as $step){
                $data = $statsRepos->findPercentOptionVoteByCoredeAndStep($corede->getId(),$step->getId());
                $results[$step->getName()] = $data;

                if(empty($created_at)){
                    $data = reset($data);
                    $created_at = $data['created_at'];
                }
            }
        }

        return array(
            'form' => $form->createView(),
            'created_at' => $created_at,
            'results' => $results,
            'statsCorede' => $statsCorede
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

        $items = $em->getRepository('PROCERGSVPRCoreBundle:Corede')->findAll();
        $map = array();
        foreach ($items as $item) {
            $map[$item->getId()] = array(
                'latitude' => $item->getLatitude(),
                'longitude' => $item->getLongitude()
            );
        }
        
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
            $entity->setLatitude($map[$line['corede_id']]['latitude']);
            $entity->setLongitude($map[$line['corede_id']]['longitude']);

            $em->persist($entity);
            $em->flush();
        }

        $response = new JsonResponse();
        $response->setData(array(
            'success' => true,
            'action' => 'update_corede_total_votes',
            'created_at' => $created_at
        ));

        return $response;
    }

    /**
     * @Route("/stats/update_total_option_votes", name="vpr_stats_update_total_option_votes")
     */
    public function updateTotalOptionVotesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $statsRepos = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalOptionVote');

        $created_at = new \DateTime();

        $coredes = $em->getRepository('PROCERGSVPRCoreBundle:Corede')->findAll();
        foreach($coredes as $corede){
            $results = $statsRepos->findTotalOptionVoteByCorede($corede);

            foreach($results as $line){
                $entity = $statsRepos->findOneBy(array('coredeId'=> $line['coredeId'],'optionId'=> $line['optionId']));
                if(!$entity){
                    $entity = new StatsTotalOptionVote();
                }

                $entity->setCoredeId($line['coredeId']);
                $entity->setOptionStepId($line['stepId']);
                $entity->setOptionNumber($line['optionNumber']);
                $entity->setOptionTitle($line['optionTitle']);
                $entity->setOptionId($line['optionId']);
                $entity->setTotalVotes($line['totalVotes']);
                $entity->setCreatedAt($created_at);

                $em->persist($entity);
                $em->flush();
            }
        }

        $response = new JsonResponse();
        $response->setData(array(
            'success' => true,
            'action' => 'update_option_total_votes',
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
        $em = $this->getDoctrine()->getManager();
        $entity = $em->createQueryBuilder()
        ->select('v')
        ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
        ->orderBy('v.totalVotes','DESC')
        ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        $response = array();
        if ($entity) {
            $response['created_at'] = $entity->getCreatedAt();
        }
        return $response;
    }
    
    /**
     * @Route("/stats/graphics/query1", name="vpr_stats_graphics_query1")
     * @Template()
     */
    public function query1Action() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQueryBuilder()
        ->select('v')
        ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
        ->orderBy('v.totalVotes','DESC')
        ->getQuery();
        
        $results = $query->getResult();
        
        $maxQuantity = 0;
        $maxAmount = 0;
        foreach ($results as $val) {
            $maxAmount += $val->getTotalVotes();
            if ($val->getTotalVotes() > $maxQuantity) {
                $maxQuantity = $val->getTotalVotes();
            }
        }
        foreach($results as $val) {
            $obj[] = array(
                "color"         => Utils::colorByQuantity($val->getTotalVotes(), $maxQuantity),
                "size"          => Utils::sizeByAmount($val->getTotalVotes(), $maxAmount),
                "quantity"      => $val->getTotalVotes(),                
                "lat"           => $val->getLatitude(),
                "long"          => $val->getLongitude(),
                "name"          => $val->getCoredeName(),
                "link_corede"   => null,
                "totReg"  => $val->getTotalWithVoterRegistration(),
                "totLc"  => $val->getTotalWithLoginCidadao(),
                "totRegLc"  => $val->getTotalWithVoterRegistrationAndLoginCidadao(),
            );
        }
        return new JsonResponse($obj);
    }
    
}