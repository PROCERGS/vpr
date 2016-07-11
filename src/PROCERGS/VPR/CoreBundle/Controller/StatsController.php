<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\PollRepository;
use PROCERGS\VPR\CoreBundle\Entity\VoteRepository;
use PROCERGS\VPR\CoreBundle\Security\VotingSessionProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\IpUtils;
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
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations as REST;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityRepository;

class StatsController extends Controller
{
    const CACHE_KEY_LAST_UPDATED = 'vpr_stats_last_updated';
    const CACHE_UPDATE_LOCK = 'vpr_stats_last_updated_running';
    const CACHE_TIME_MINUTES = 15;

    /**
     * @Route("/reports/corede/{coredeId}/{pollId}", name="vpr_report_voted_options_by_corede")
     * @Template()
     */
    public function reportOptionsCoredeAction(Request $request, $coredeId, $pollId)
    {
        $this->checkAccess($request);

        $em = $this->getDoctrine()->getManager();
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        $poll = $pollRepo->find($pollId);

        $form = $this->getCoredeForm();
        $form->get('poll')->setData($poll);

        $params = array(
            'form' => $form->createView(),
            'coredes' => $this->getCoredes(),
            'updateCache' => $this->shouldUpdateCache(),
        );
        
        $connection = $em->getConnection();
        $sql = "
select a1.name corede_name, sum(a2.tot_voters_online + a2.tot_voters_offline + a2.tot_voters_sms) total_votes
from corede a1
left join stats_prev_ppp a2 on a1.id = a2.corede_id
where a2.poll_id = ? and a2.corede_id = ?
group by a1.name
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll->getId(), $coredeId));
        $statsCorede = current($stmt1->fetchAll(\PDO::FETCH_ASSOC));
        
        $sql = "
select a2.name, a1.title option_title, a1.category_sorting option_number, sum(a3.tot) total
from poll_option a1
inner join step a2 on a1.step_id = a2.id
left join stats_prev_ppp2 a3 on a1.id = a3.option_id
where a3.poll_id = ? and a3.corede_id = ?
group by a2.name, a1.title, a1.category_sorting 
order by a1.category_sorting
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll->getId(), $coredeId));
        $results = $stmt1->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);
        $created_at = new \DateTime();
        $params['created_at'] = $created_at;
        $params['results'] = $results;
        $params['statsCorede'] = $statsCorede;

        return $this->render(
            'PROCERGSVPRCoreBundle:Stats:optionVotes.html.twig',
            $params
        );
    }

    /**
     * @Route("/reports/city/{cityId}/{pollId}", name="vpr_report_voted_options_by_city")
     * @Template()
     */
    public function reportOptionsCityAction(Request $request, $cityId, $pollId)
    {
        $this->checkAccess($request);
        $em = $this->getDoctrine()->getManager();
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        $poll = $pollRepo->find($pollId);

        $form = $this->getCityForm();
        $form->get('poll')->setData($poll);
        $params = array(
            'form' => $form->createView(),
            'cities' => $this->getCities(),
            'updateCache' => $this->shouldUpdateCache(),
        );
        $city = $em->getRepository('PROCERGSVPRCoreBundle:City')->findOneById($cityId);
        $connection = $em->getConnection();
        $sql = "
select a1.name city_name, sum(a2.tot_voters_online + a2.tot_voters_offline + a2.tot_voters_sms) total
from city a1
left join stats_prev_ppp a2 on a1.id = a2.city_id
where a2.poll_id = ? and a2.city_id = ?
group by a1.name
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll->getId(), $cityId));
        $tot = current($stmt1->fetchAll(\PDO::FETCH_ASSOC));
        $cityTotal = null;
        if ($tot) {
            $cityTotal = $tot['total'];
        }
        
        $sql = "
select a2.name, a1.title option_title, a1.category_sorting option_number, sum(a3.tot) total
from poll_option a1
inner join step a2 on a1.step_id = a2.id
left join stats_prev_ppp2 a3 on a1.id = a3.option_id
where a3.poll_id = ? and a3.city_id = ?
group by a2.name, a1.title, a1.category_sorting
order by a1.category_sorting
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll->getId(), $cityId));
        $results = $stmt1->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);
        
        $params['results'] = $results;
        $params['city'] = $city;
        $params['cityTotal'] = $cityTotal;


        return $this->render(
            'PROCERGSVPRCoreBundle:Stats:optionVotesCity.html.twig',
            $params
        );
    }

    /**
     * @Route("/reports/corede", name="vpr_option_vote_by_corede")
     * @Template()
     */
    public function optionVotesAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_RESULTS');
        $this->updateCacheAction($request);
        $form = $this->getCoredeForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $name = trim($data['corede']);
            $coredeRepo = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
            $corede = $coredeRepo->findOneByName($name);
            $poll = $data['poll'];

            if (strlen($name) > 0) {
                $url = $this->generateUrl(
                    'vpr_report_voted_options_by_corede',
                    array('coredeId' => $corede->getId(), 'pollId' => $poll->getId())
                );

                return $this->redirect($url);
            }
        }

        $form = $form->createView();
        $coredes = $this->getCoredes();
        $updateCache = $this->shouldUpdateCache();

        return compact('form', 'coredes', 'updateCache');
    }

    /**
     * @Route("/reports/city", name="vpr_option_vote_by_city")
     * @Template()
     */
    public function optionVotesCityAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_RESULTS');
        $this->updateCacheAction($request);
        $form = $this->getCityForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $name = trim($data['city']);
            $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
            $city = $cityRepo->findOneByName($name);
            $poll = $data['poll'];

            if (strlen($name) > 0) {
                $url = $this->generateUrl(
                    'vpr_report_voted_options_by_city',
                    array('cityId' => $city->getId(), 'pollId' => $poll->getId())
                );

                return $this->redirect($url);
            }
        }

        $form = $form->createView();
        $cities = $this->getCities();
        $updateCache = $this->shouldUpdateCache();

        return compact('form', 'cities', 'updateCache');
    }

    /**
     * @Route("/stats/votes", name="vpr_stats_votes")
     * @Template()
     */
    public function votesAction(Request $request)
    {
        $this->checkAccess($request);
        $this->updateCacheAction($request);
        $em = $this->getDoctrine()->getManager();

        $session = $this->getRequest()->getSession();

        $poll_filters = $session->get('poll_filters');
        $form = $this->createForm(new PollOptionFilterType());
        $form->remove("corede");


        if ($request->isMethod('POST') || $poll_filters) {
            if (!$request->isMethod('POST') && $poll_filters) {
                $form->bind($poll_filters);
            } else {
                $form->bind($request);
                $session->set('poll_filters', $request);
            }
            $selected = $form->getData();

            $poll = $selected['poll'];
            if (!$poll) {
                $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
            }
        } else {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        }


        $query = $em->createQueryBuilder()
            ->select(
                'v.coredeName,
                      sum(v.totalWithVoterRegistration) as totalWithVoterRegistration,
                      sum(v.totalWithLoginCidadao) as totalWithLoginCidadao,
                      sum(v.totalWithVoterRegistrationAndLoginCidadao) as totalWithVoterRegistrationAndLoginCidadao,
                      sum(v.totalVotes) as totalVotes'
            )
            ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
            ->join('PROCERGSVPRCoreBundle:BallotBox', 'b', 'WITH', 'b.id = v.ballotBoxId')
            ->where('b.poll = :poll')
            ->groupBy('v.coredeId, v.coredeName')
            ->orderBy('totalVotes', 'DESC')
            ->getQuery()->setParameters(array("poll" => $poll->getId()));

        $results = $query->getResult();

        $created_at = $em->createQueryBuilder()
            ->select('MAX(v.createdAt)')
            ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
            ->join('PROCERGSVPRCoreBundle:BallotBox', 'b', 'WITH', 'b.id = v.ballotBoxId')
            ->where('b.poll = :poll')
            ->getQuery()->setParameters(array("poll" => $poll->getId()))->setMaxResults(1)->getOneOrNullResult();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($results, 'json');


        return array(
            'results' => $results,
            'jsonContent' => $jsonContent,
            'created_at' => $created_at[1],
            'form' => $form->createView(),
        );
    }

    private function getCityForm()
    {
        return $this->createFormBuilder()->add(
            'city',
            'text',
            array(
                'required' => true,
                'label' => 'form.city.select',
            )
        )
            ->add(
                'poll',
                'entity',
                array(
                    'class' => 'PROCERGSVPRCoreBundle:Poll',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.openingTime', 'DESC');
                    },
                    'property' => 'name',
                    'required' => true,
                )
            )
            ->add('submit', 'submit')->getForm();
    }

    private function getCoredeForm()
    {
        return $this->createFormBuilder()->add(
            'corede',
            'text',
            array(
                'required' => true,
                'label' => 'form.corede.select',
            )
        )
            ->add(
                'poll',
                'entity',
                array(
                    'class' => 'PROCERGSVPRCoreBundle:Poll',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.openingTime', 'DESC');
                    },
                    'property' => 'name',
                    'required' => true,
                )
            )
            ->add('submit', 'submit')->getForm();
    }

    private function getCities()
    {
        $serializer = $this->container->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');

        return $serializer->serialize(
            $cityRepo->findAll(),
            'json',
            SerializationContext::create()->setSerializeNull(true)->setGroups(
                array(
                    'autocomplete',
                )
            )
        );
    }

    private function getCoredes()
    {
        $serializer = $this->container->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $coredeRepo = $em->getRepository('PROCERGSVPRCoreBundle:Corede');

        return $serializer->serialize(
            $coredeRepo->findAll(),
            'json',
            SerializationContext::create()->setSerializeNull(true)->setGroups(
                array(
                    'vote',
                )
            )
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
    public function updateTotalVotesAction(Request $request)
    {
        $this->checkAccess($request);
        $em = $this->getDoctrine()->getManager();
        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        $poll = $pollRepo->findLastPoll();

        $results = $statsRepo->findTotalVotes();
        $created_at = new \DateTime();

        $coredes = $em->getRepository('PROCERGSVPRCoreBundle:Corede')->findAll();
        $map = array();
        foreach ($coredes as $corede) {
            $map[$corede->getId()] = array(
                'latitude' => $corede->getLatitude(),
                'longitude' => $corede->getLongitude(),
            );
        }

        foreach ($results as $line) {
            $entity = $statsRepo->findOneByCoredeId($poll, $line['corede_id']);
            if (!$entity) {
                $entity = new StatsTotalCoredeVote();
            }

            $entity->setBallotBoxId($line['ballot_box_id']);
            $entity->setCoredeId($line['corede_id']);
            $entity->setCoredeName($line['corede_name']);
            $entity->setTotalWithVoterRegistration($line['total_with_voter_registration']);
            $entity->setTotalWithLoginCidadao($line['total_with_login_cidadao']);
            $entity->setTotalWithVoterRegistrationAndLoginCidadao(
                $line['total_with_voter_registration_and_login_cidadao']
            );
            $entity->setTotalVotes($line['total_votes']);
            $entity->setCreatedAt($created_at);
            $entity->setLatitude($map[$line['corede_id']]['latitude']);
            $entity->setLongitude($map[$line['corede_id']]['longitude']);

            $em->persist($entity);
            $em->flush();
        }

        $response = new JsonResponse();
        $response->setData(
            array(
                'success' => true,
                'action' => 'update_corede_total_votes',
                'created_at' => $created_at,
            )
        );

        return $response;
    }

    /**
     * @Route("/stats/update_total_option_votes", name="vpr_stats_update_total_option_votes")
     */
    public function updateTotalOptionVotesAction(Request $request)
    {
        $this->checkAccess($request);
        $em = $this->getDoctrine()->getManager();
        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalOptionVote');
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();

        $created_at = new \DateTime();
        /**
         * @var \Doctrine\DBAL\Connection $connection
         */
        $connection = $em->getConnection();
        try {
            $connection->beginTransaction();
            $sql = "delete from stats_total_option_vote where poll_id = ? ";
            $stmt1 = $connection->prepare($sql);
            $stmt1->execute(array($poll->getId()));
            $sql = "
            with tb1 as (
                SELECT s0_.poll_id
                , s0_.corede_id
                , s0_.poll_option_id
                , count(s0_.poll_option_id) tot
                FROM stats_option_vote s0_
                WHERE
                s0_.poll_id = ?
                group by s0_.poll_id
                , s0_.corede_id
                , s0_.poll_option_id
                )
                insert into stats_total_option_vote (id, poll_id, corede_id, option_step_id, option_id, option_number, option_title, total_votes, created_at)
                SELECT
                nextval('stats_total_option_vote_id_seq')
                ,s0_.poll_id
                , s0_.corede_id
                , p2_.step_id
                , p2_.id
                , p2_.category_sorting
                , p2_.title
                , sum(s0_.tot)
                , now_test()
                FROM tb1 s0_
                INNER JOIN poll_option p2_ ON (s0_.poll_option_id = p2_.id)
                GROUP BY s0_.poll_id, s0_.corede_id, p2_.step_id, p2_.id, p2_.category_sorting, p2_.title
            ";
            $stmt1 = $connection->prepare($sql);
            $stmt1->execute(array($poll->getId()));
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
        $response = new JsonResponse();
        $response->setData(
            array(
                'success' => true,
                'action' => 'update_option_total_votes',
                'created_at' => $created_at,
            )
        );

        return $response;
    }

    /**
     * @Route("/stats/votes_by_corede", name="vpr_stats_votes_by_corede")
     */
    public function votesByCoredeAction(Request $request)
    {
        $this->checkAccess($request);
        $this->updateCacheAction($request);
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('v')
            ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
            ->orderBy('v.totalVotes', 'DESC')
            ->getQuery();

        $results = $query->getResult();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($results, 'json');

        $response = new Response($jsonContent);
        $response->headers->add(array('Content-Type' => 'application/json'));

        return $response;
    }

    /**
     * @Route("/stats/graphics", name="vpr_stats_graphics")
     * @Template()
     */
    public function graphicsAction(Request $request)
    {
        $this->checkAccess($request);
        $this->updateCacheAction($request);
        $em = $this->getDoctrine()->getManager();

        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();

        $entity = $em->createQueryBuilder()
            ->select('v')
            ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
            ->join('PROCERGSVPRCoreBundle:BallotBox', 'b', 'WITH', 'b.id = v.ballotBoxId')
            ->where('b.poll = :poll')
            ->orderBy('v.totalVotes', 'DESC')
            ->getQuery()->setParameters(array("poll" => $poll->getId()))->setMaxResults(1)->getOneOrNullResult();
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
    public function query1Action(Request $request)
    {
        $this->checkAccess($request);
        $this->updateCacheAction($request);
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        $query = $em->createQueryBuilder()
            ->select('v')
            ->from('PROCERGSVPRCoreBundle:StatsTotalCoredeVote', 'v')
            ->join('PROCERGSVPRCoreBundle:BallotBox', 'b', 'WITH', 'b.id = v.ballotBoxId')
            ->where('b.poll = :poll')
            ->orderBy('v.totalVotes', 'DESC')
            ->getQuery()->setParameters(array("poll" => $poll->getId()));

        $results = $query->getResult();

        $maxQuantity = 0;
        $maxAmount = 0;
        foreach ($results as $val) {
            $maxAmount += $val->getTotalVotes();
            if ($val->getTotalVotes() > $maxQuantity) {
                $maxQuantity = $val->getTotalVotes();
            }
        }
        foreach ($results as $val) {
            $obj[] = array(
                "color" => Utils::colorByQuantity(
                    $val->getTotalVotes(),
                    $maxQuantity
                ),
                "size" => Utils::sizeByAmount($val->getTotalVotes(), $maxAmount),
                "quantity" => $val->getTotalVotes(),
                "lat" => $val->getLatitude(),
                "long" => $val->getLongitude(),
                "name" => $val->getCoredeName(),
                "link_corede" => null,
                "totReg" => $val->getTotalWithVoterRegistration(),
                "totLc" => $val->getTotalWithLoginCidadao(),
                "totRegLc" => $val->getTotalWithVoterRegistrationAndLoginCidadao(),
            );
        }

        return new JsonResponse($obj);
    }

    private function shouldUpdateCache()
    {
        if (!$this->has('session.memcached')) {
            return false;
        }
        $cache = $this->get('session.memcached');

        $lastUpdated = $cache->get(self::CACHE_KEY_LAST_UPDATED);
        $locked = $cache->get(self::CACHE_UPDATE_LOCK);
        if ($lastUpdated !== false || $locked !== false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @Route("/reports/update/corede", name="vpr_update_corede_report_cache")
     * @template()
     */
    public function updateCacheAction(Request $request)
    {
        $this->checkAccess($request);
        if ($this->shouldUpdateCache()) {
            $cache = $this->get('session.memcached');

            $cache->set(self::CACHE_UPDATE_LOCK, true, null, 60);
            try {
                $this->updateTotalOptionVotesAction($request);
                $this->updateTotalVotesAction($request);

                $lastUpdated = new \DateTime();
                $cache->set(
                    self::CACHE_KEY_LAST_UPDATED,
                    $lastUpdated,
                    null,
                    60 * self::CACHE_TIME_MINUTES
                );
                $cache->delete(self::CACHE_UPDATE_LOCK);
            } catch (Exception $e) {
                $cache->delete(self::CACHE_UPDATE_LOCK);
            }

            return array('lastUpdated' => $cache->get(self::CACHE_KEY_LAST_UPDATED));
        } else {
            if ($this->has('session.memcached')) {
                $cache = $this->get('session.memcached');

                return array('lastUpdated' => $cache->get(self::CACHE_KEY_LAST_UPDATED));
            } else {
                return array('lastUpdated' => new \DateTime());
            }
        }
    }

    /**
     * @Route("/stats/live", name="vpr_stats_votes_per_minute_live")
     * @Template
     */
    public function votesPerMinuteAction(Request $request)
    {
        $this->checkAccess($request);

        $ballotBox = $request->get('ballotbox', null);
        if ($ballotBox !== null && strtolower($ballotBox) === 'sms') {
            $poll = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
            /** @var VotingSessionProvider $votingSessionProvider */
            $votingSessionProvider = $this->get('vpr_voting_session_provider');
            $ballotBox = $votingSessionProvider->getSmsBallotBox($poll);
            $dataUrl = $this->generateUrl('vpr_stats_vpm_data', ['ballotbox' => $ballotBox->getId()]);
        } else {
            $dataUrl = $this->generateUrl('vpr_stats_vpm_data');
        }

        $data = $this->getVotesPerMinute($ballotBox);

        return compact('data', 'dataUrl');
    }

    /**
     * @REST\Get("/stats/live/data", name="vpr_stats_vpm_data")
     * @REST\View
     */
    public function votesPerMinuteDataAction(Request $request)
    {
        $this->checkAccess($request);

        $ballotBoxId = $request->get('ballotbox', null);
        if ($ballotBoxId !== null) {
            $repo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:BallotBox');
            $ballotBox = $repo->find($ballotBoxId);
        } else {
            $ballotBox = null;
        }

        $data = $this->getVotesPerMinute($ballotBox);

        return new JsonResponse($data);
    }

    private function getVotesPerMinute(BallotBox $ballotBox = null)
    {
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        $ballotBoxId = $ballotBox instanceof BallotBox ? '_b'.$ballotBox->getId() : '';
        $cacheKey = "votes_per_minute_{$poll->getId()}".$ballotBoxId;
        $data = $this->getCached(
            $cacheKey,
            30,
            function () use ($em, $poll, $ballotBox) {
                /** @var VoteRepository $repo */
                $repo = $em->getRepository('PROCERGSVPRCoreBundle:Vote');
                $vpm = $repo->getVotesPerMinute($poll, $ballotBox);

                $data = array_map(
                    function ($minute) {
                        $minute['time'] = sprintf(
                            '%s-%s-%s %s:%s',
                            $minute['year'],
                            str_pad($minute['month'], 2, '0', STR_PAD_LEFT),
                            str_pad($minute['day'], 2, '0', STR_PAD_LEFT),
                            str_pad($minute['hour'], 2, '0', STR_PAD_LEFT),
                            str_pad($minute['minute'], 2, '0', STR_PAD_LEFT)
                        );

                        $minute['y'] = $minute['votes'];

                        return $minute;
                    },
                    $vpm
                );

                return $data;
            }
        );

        return $data;
    }

    /**
     * @Route("/stats/ballotboxes", name="vpr_stats_ballotboxes")
     * @Template
     */
    public function ballotBoxesAction(Request $request)
    {
        $this->checkAccess($request);
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();

        $cacheKey = "ballotboxes_{$poll->getId()}";

        $ballotBoxes = $this->getCached(
            $cacheKey,
            15,
            function () use ($poll, $em) {
                return $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')
                    ->getActivationStatistics($poll);
            }
        );
        $data = $this->groupBallotBoxes($ballotBoxes);
        $total = count($ballotBoxes);

        return compact('data', 'total');
    }

    /**
     * @Route("/stats/ballotboxes/admin", name="vpr_stats_ballotboxes_admin")
     * @Template
     */
    public function ballotBoxesAdminAction(Request $request)
    {
        $this->checkAccess($request);
        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();

        $cacheKey = "ballotboxes_{$poll->getId()}";

        $ballotBoxes = $this->getCached(
            $cacheKey,
            15,
            function () use ($poll, $em) {
                return $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')
                    ->getActivationStatistics($poll);
            }
        );

        $data = $this->groupBallotBoxes($ballotBoxes);
        $total = count($ballotBoxes);

        return compact('data', 'total');
    }

    private function groupBallotBoxes($data)
    {
        $result = array(
            'idle' => array(),
            'activated' => array(),
            'finished' => array(),
        );

        foreach ($data as $ballotBox) {
            if ($ballotBox['setupAt'] === null) {
                $status = 'idle';
            } elseif ($ballotBox['closedAt'] === null) {
                $status = 'activated';
            } else {
                $status = 'finished';
            }

            $result[$status][] = $ballotBox;
        }

        return $result;
    }

    /**
     * @return \Memcache
     */
    private function getMemcached()
    {
        return $this->get('session.memcached');
    }

    private function getCached($cacheKey, $timeout, callable $fetchDataCallback)
    {
        $cache = $this->getMemcached();
        $cacheLockKey = "{$cacheKey}_lock";

        $cached = $cache->get($cacheKey);
        $locked = $cache->get($cacheLockKey);
        $expired = !$cached || $cached['expires'] < time();
        if ($expired) {
            if (!$locked) {
                $go = true;
            } else {
                $t = time() - $cached['expires'];

                if ($t > ($timeout * 2)) {
                    $go = true;
                    $cache->set($cacheLockKey, null);
                } else {
                    $go = false;
                }
            }

            if ($go) {
                $cache->set($cacheLockKey, date('Y-m-d H:i:s'));
                $data = $fetchDataCallback();

                $cached = array(
                    'expires' => time() + $timeout,
                    'data' => $data,
                );
                $cache->set($cacheKey, $cached, MEMCACHE_COMPRESSED);
                $cache->set($cacheLockKey, null);
            }

        } else {
            $data = $cached['data'];
        }

        return $data;
    }

    /**
     * Clear Filters
     * @Method("GET")
     * @Route("/filters/clear", name="admin_stats_clear_filters")
     */
    public function clearFiltersAction()
    {
        $session = $this->getRequest()->getSession();
        $session->remove('poll_filters');

        return $this->redirect($this->generateUrl('vpr_stats_votes'));
    }

    private function checkAccess(Request $request)
    {
        /** @var LoggerInterface $logger */
        $logger = $this->get('monolog.logger.stats_security');

        $allowed = $this->getParameter('allowed_monitors');
        $clientIp = $request->getClientIp();

        if (IpUtils::checkIp($clientIp, $allowed)) {
            // Allow monitors to access without authentication
            $logger->info('Allowed access to '.$clientIp.' at '.$request->getRequestUri());

            return;
        } else {
            $logger->info($clientIp.' not allowed by IP at '.$request->getRequestUri().'. Testing ROLE_STATS...');
        }

        $this->denyAccessUnlessGranted('ROLE_STATS');
    }


    /**
     * @Route("/stats/ip", name="vpr_stats_votes_per_ip")
     * @Template
     */
    public function votesPerIpAction(Request $request)
    {
        $this->checkAccess($request);

        $em = $this->getDoctrine()->getManager();
        $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();

        $coredeId = '';
        $cityId = '';
        $cacheKey = "votes_per_ip_{$poll->getId()}".$coredeId.$cityId;

        $data = $this->getCached(
            $cacheKey,
            60,
            function () use ($em, $poll) {
                /** @var VoteRepository $repo */
                $repo = $em->getRepository('PROCERGSVPRCoreBundle:Vote');

                return $repo->getVotesPerIp($poll, null, null, 10);
            }
        );

        $cities = [];
        foreach ($data as $entry) {
            $cities[$entry['city_id']] = $entry['city'];
        }
        asort($cities);

        return compact('data', 'cities');
    }
}
