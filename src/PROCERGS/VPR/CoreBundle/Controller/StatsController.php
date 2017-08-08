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
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Entity\BallotBoxRepository;
use PROCERGS\VPR\CoreBundle\Entity\StatsTotalCoredeVoteRepository;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\BallotBoxFilterType;
use PROCERGS\VPR\CoreBundle\Entity\RlCriterioRepository;
use PROCERGS\VPR\CoreBundle\Entity\Corede;
use PROCERGS\VPR\CoreBundle\Entity\CityRepository;

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
            'updateCache' => null,
        );
        
        $connection = $em->getConnection();
        $sql = "
select a1.name corede_name, sum(a2.tot_voters_online + a2.tot_voters_offline + a2.tot_voters_sms) total_votes
from corede a1
left join stats_prev_ppp a2 on a1.id = a2.corede_id and a2.poll_id = ? 
where a1.id = ?
group by a1.name
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll->getId(), $coredeId));
        $statsCorede = current($stmt1->fetchAll(\PDO::FETCH_ASSOC));
        
        $sql = "
select a2.name, a1.title option_title, a1.category_sorting option_number, sum(a3.tot) total, a2.sorting
from poll_option a1
inner join step a2 on a1.step_id = a2.id
left join stats_prev_ppp2 a3 on a1.id = a3.option_id
where a2.poll_id = ? and a1.corede_id = ?
group by a2.name, a1.title, a2.sorting, a1.category_sorting 
order by a2.sorting, a1.category_sorting
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll->getId(), $coredeId));
        if ($request->get('csv')) {
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="'.$pollId . '_' .$coredeId.'";'
            );
            $response->sendHeaders();
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('corede_id', 'etapa', 'opcao', 'total'), $sep);
            while ($linha = $stmt1->fetch(\PDO::FETCH_ASSOC)) {
                fputcsv($output, array($coredeId, utf8_decode($linha['name']), $linha['option_number'] . ' - '. utf8_decode($linha['option_title']), $linha['total']), $sep);
            }
            fputcsv($output, array($coredeId, 'Total Votantes', '', $statsCorede['total_votes']), $sep);
            return $response;
        } else {
            $results = $stmt1->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);
        }
        $created_at = new \DateTime();
        $params['created_at'] = $created_at;
        $params['results'] = $results;
        $params['statsCorede'] = $statsCorede;
        $params['coredeId'] = $coredeId;
        $params['pollId'] = $pollId;

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
            'updateCache' => null,
        );
        $city = $em->getRepository('PROCERGSVPRCoreBundle:City')->findOneById($cityId);
        $connection = $em->getConnection();
        $sql = "
select a1.name city_name, sum(a2.tot_voters_online + a2.tot_voters_offline + a2.tot_voters_sms) total
from city a1
left join stats_prev_ppp a2 on a1.id = a2.city_id and a2.poll_id = ? 
where a1.id = ?
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
select a2.name, a1.title option_title, a1.category_sorting option_number, sum(a3.tot) total, a2.sorting
from poll_option a1
inner join step a2 on a1.step_id = a2.id
left join stats_prev_ppp2 a3 on a1.id = a3.option_id
inner join city a4 on a4.corede_id = a1.corede_id
where a2.poll_id = ? and a4.id = ?
group by a2.name, a1.title, a2.sorting, a1.category_sorting
order by a2.sorting, a1.category_sorting
        ";
        $stmt1 = $connection->prepare($sql);
        $stmt1->execute(array($poll->getId(), $cityId));
        
        if ($request->get('csv')) {
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="'.$pollId . '_' .$city->getIbgeCode().'";'
            );
            $response->sendHeaders();
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('ibge_code', 'etapa', 'opcao', 'total'), $sep);
            while ($linha = $stmt1->fetch(\PDO::FETCH_ASSOC)) {
                fputcsv($output, array($city->getIbgeCode(), utf8_decode($linha['name']), $linha['option_number'] . ' - '. utf8_decode($linha['option_title']), $linha['total']), $sep);
            }   
            fputcsv($output, array($city->getIbgeCode(), 'Total Votantes', '', $cityTotal), $sep);
            return $response;
        } else {
            $results = $stmt1->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);
        }
        
        $params['results'] = $results;
        $params['city'] = $city;
        $params['cityTotal'] = $cityTotal;
        $params['pollId'] = $pollId;


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
                'v.coredeId,v.coredeName,
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
        if ($request->get('csv')) {
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="'.$poll->getId().'";'
            );
            $response->sendHeaders();
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('corede_id', 'corede_name', utf8_decode('Título'), utf8_decode('Login Cidadão'), utf8_decode('Título e Login Cidadão'), 'Total'), $sep);
            foreach ($results as $linha) {
                fputcsv($output, array($linha['coredeId'], utf8_decode($linha['coredeName']), $linha['totalWithVoterRegistration'], $linha['totalWithLoginCidadao'], $linha['totalWithVoterRegistrationAndLoginCidadao'], $linha['totalVotes']), $sep);
            }
            return $response;
        }

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
     * @Route("/stats/votes_by_corede", name="vpr_stats_votes_by_corede")
     */
    public function votesByCoredeAction(Request $request)
    {
        $this->checkAccess($request);
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
     * @Route("/stats/ballotboxes/admin", name="vpr_stats_ballotboxes_admin")
     * @Template
     */
    public function ballotBoxesAdminAction(Request $request)
    {
        $this->checkAccess($request);
        
        $em = $this->getDoctrine()->getManager();        
        $connection = $em->getConnection();
        $session = $this->getRequest()->getSession();
        $form = $this->createForm(new BallotBoxFilterType());
        $form->remove('is_sms')->remove('is_online');
        $isCsv = $request->get('csv');
        
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $session->set('ballotBox_filters', $request);
        } else {
            $request = $session->get('ballotBox_filters');
            if ($request) {
                $form->handleRequest($request);
            }
        }

        $filters = $form->getData();

        $params = array();
        $sql = "select 
            a1.setup_at
            , a1.closed_at
            , a1.pin
            , a1.city_id
            , a2.name city_name
            , a2.corede_id
            , a2.ibge_code
            , a3.name corede_name
            , case 
            when a1.setup_at is null then '".BallotBox::getAllowedStatus1(1)."' 
            when a1.setup_at is not null and a1.closed_at is null then '".BallotBox::getAllowedStatus1(2)."'
            when a1.setup_at is not null and a1.closed_at is not null then '".BallotBox::getAllowedStatus1(3)."'
            end status1_label
            , case 
            when a1.setup_at is null then 1 
            when a1.setup_at is not null and a1.closed_at is null then 2
            when a1.setup_at is not null and a1.closed_at is not null then 3
            end status1
            from ballot_box a1 
            inner join city a2 on a2.id = a1.city_id 
            inner join corede a3 on a3.id = a2.corede_id 
            where a1.poll_id = :poll and a1.is_online = false and a1.is_sms = false ";
        if (isset($filters['poll'])) {
            $params['poll'] = $filters['poll']->getId();
        } else if (!isset($filters)) {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
            $params['poll'] = $poll->getId();
            $form->get('poll')->setData($poll);
        }
        if (isset($filters['city'])) {
            $sql .= "and a1.city_id = :city ";
            $params['city'] = $filters['city']->getId();
        }

        switch ($filters['status1']) {
            case 1:
                $sql .= 'and a1.setup_at is null ';
                break;
            case 2:
                $sql .= 'and a1.setup_at is not null and a1.closed_at is null ';
                break;
            case 3:
                $sql .= 'and a1.setup_at is not null and a1.closed_at is not null ';
                break;
        }
        if ($filters['pin']) {
            $sql .= 'and a1.pin = :pin ';
            $params['pin'] = $filters['pin'];
        }

        if ($filters['email']) {
            $sql .= 'and a1.email = :email ';
            $params['email'] = $filters['email'];
        }
        if ($filters['name']) {
            $sql .= 'and lower(b.name) LIKE lower(:name) ';
            $params['name'] = '%'.$filters['name'].'%';
        }
        $sql .= "order by a2.corede_id, a1.city_id, status1, a1.pin ";

        $stmt1 = $connection->prepare($sql);
        $a = $stmt1->execute($params);
        if ($isCsv) {
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="'.$params['poll'].'";'
            );
            $response->sendHeaders();
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('corede_id', 'corede', 'cidade_ibge_code', 'cidade', 'status_code', 'status', 'pin'), $sep);
            while ($linha = $stmt1->fetch(\PDO::FETCH_ASSOC)) {
                fputcsv($output, array($linha['corede_id'], utf8_decode($linha['corede_name']), $linha['ibge_code'], utf8_decode($linha['city_name']), $linha['status1'], utf8_decode($linha['status1_label']), $linha['pin']), $sep);
            }
            return $response;
        }
        $entities = $stmt1->fetchAll(\PDO::FETCH_ASSOC);
        $data = $this->groupBallotBoxes($entities);
        $total = count($entities);
        
        return array(
            'data' => $data,
            'total' => $total,
            'entities' => $entities,
            'form' => $form->createView(),
        );
    }

    private function groupBallotBoxes(&$data)
    {
        $result = array(
            'idle' => array(),
            'activated' => array(),
            'finished' => array(),
        );
    
        foreach ($data as &$ballotBox) {
            if ($ballotBox['setup_at'] === null) {
                $status = 'idle';
            } elseif ($ballotBox['closed_at'] === null) {
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

        /* @var $repo VoteRepository */
        $repo = $em->getRepository('PROCERGSVPRCoreBundle:Vote');
        
        $data = $repo->getVotesPerIp($poll, null, null, 10);
        if ($request->get('csv')) {            
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="'.$poll->getId() .'";'
            );
            $response->sendHeaders();
            
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('ip', 'corede_id', 'corede', 'cidade_ibge_code', 'cidade', 'total'), $sep);
            foreach ($data as $linha) {
                fputcsv($output, array($linha['ipAddress'], $linha['corede_id'], utf8_decode($linha['corede']), $linha['city_ibge_code'], utf8_decode($linha['city']), $linha['votes']), $sep);
            }
            return $response;
        }
        $cities = [];
        foreach ($data as $entry) {
            $cities[$entry['city_id']] = $entry['city'];
        }
        asort($cities);

        return compact('data', 'cities');
    }
    
    /**
     * @Route("/rl/eleitores-municipio", name="vpr_rl_eleitores_municipio")
     * @Template()
     */
    public function missioEleitoresMunicipioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo RlCriterioRepository */        
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
        /* @var $coredeRepo Corede */
        $coredeRepo = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
        /* @var $cityRepo CityRepository */
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        $filter = $this->montaFilterMissio();
        $filter1 = $this->get('session')->get('filter_missio');
        if ($request->isMethod('POST')) {
            $filter['poll_id'] = $request->get('poll_id');
            $filter['corede_id'] = $request->get('corede_id');
            $filter['city_id'] = $request->get('city_id');
            $entities1 = $rlCriterioRepo->findEspecial2($filter);
            $this->get('session')->set('filter_missio', $filter);
        } elseif ($filter1) {
            $filter = $filter1;
            $entities1 = $rlCriterioRepo->findEspecial2($filter);
        } else {
            $entities1 = array();
        }
        $polls = $pollRepo->findBy(array(),array('openingTime' => 'desc'));
        $coredes = $coredeRepo->findBy(array(),array('name' => 'asc'));
        if ($filter['corede_id']) {
            $citys = $cityRepo->findCombo1($filter['corede_id']);
        } else {
            $citys = array();
        }
        $allCitys = $cityRepo->findCombo1();
        return array(
            'entities1' => $entities1,
            'polls' => $polls,
            'coredes' => $coredes,
            'citys' => $citys,
            'allCitys' => $allCitys,
            'filter' => $filter,
        );
    }
    /**
     * @Route("/rl/eleitores-municipio-csv", name="vpr_rl_eleitores_municipio_csv")
     */
    public function missioEleitoresMunicipioCsvAction(Request $request)
    {
        $filter1 = $this->get('session')->get('filter_missio');
        if (isset($filter1['poll_id'])) {
            $em = $this->getDoctrine()->getManager();
            /* @var $rlCriterioRepo RlCriterioRepository */
            $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
            $entities = $rlCriterioRepo->findEspecial2($filter1);
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="eleitores_municipio_'.$filter1['poll_id'] .'.csv";'
                );
            $response->sendHeaders();
    
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('COREDE_ID'
                , 'COREDE_NOME'
                , 'MUNICIPIOS_ID'
                , 'MUNICIPIOS_NOME'
                , 'MUNICIPIOS_IBGE'
                , 'N_ELEITORES'
                , 'N_VOTANTES'
                , 'PERCENTUAL_CORTE_MUNICIPIOS'
                , 'CORTE_MUNICIPIOS'
                , 'CORTE_FAIXA_ANTECEDENTE'
                , 'PERCENTUAL_CORTE_PROGRAMAS'
                , 'CORTE_PROGRAMAS'
                , 'TOTAL_DE_VOTOS'
                , 'PERCENTUAL_DE_VOTACAO'
                , 'STATUS'
                , 'N_PROGRAMAS_CLASSIFICADOS'
            ), $sep);
            foreach ($entities as $linha) {
                fputcsv($output, array($linha['corede_id']
                    , utf8_decode($linha['corede_name'])
                    , $linha['city_id']                    
                    , utf8_decode($linha['city_name'])
                    , $linha['ibge_code']
                    , $linha['tot_pop']
                    , $linha['voters_total']
                    , $linha['perc_pop']
                    , $linha['corte_mun']
                    , $linha['corte_ult_criterio']
                    , $linha['perc_prog']
                    , $linha['corte_prog']
                    , $linha['votes_total']
                    , $linha['voters_perc']
                    , utf8_decode($linha['status_corte_mun'])
                    , $linha['tot_prog_classificados']
                ), $sep);
            }
            return $response;
        }
    }
    
    /**
     * @Route("/rl/votos", name="vpr_rl_votos")
     * @Template()
     */
    public function missioVotosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo RlCriterioRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
        /* @var $coredeRepo Corede */
        $coredeRepo = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
        /* @var $cityRepo CityRepository */
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        $filter = $this->montaFilterMissio();
        $filter1 = $this->get('session')->get('filter_missio');
        if ($request->isMethod('POST')) {
            $filter['poll_id'] = $request->get('poll_id');
            $filter['corede_id'] = $request->get('corede_id');
            $filter['city_id'] = $request->get('city_id');
            $entities1 = $rlCriterioRepo->findEspecial3($filter)->fetchAll(\PDO::FETCH_ASSOC);;
            $this->get('session')->set('filter_missio', $filter);
        } elseif ($filter1) {
            $filter = $filter1;
            $entities1 = $rlCriterioRepo->findEspecial3($filter)->fetchAll(\PDO::FETCH_ASSOC);;
        } else {
            $entities1 = array();
        }
        $polls = $pollRepo->findBy(array(),array('openingTime' => 'desc'));
        $coredes = $coredeRepo->findBy(array(),array('name' => 'asc'));
        if ($filter['corede_id']) {
            $citys = $cityRepo->findCombo1($filter['corede_id']);
        } else {
            $citys = array();
        }
        $allCitys = $cityRepo->findCombo1();
        return array(
            'entities1' => $entities1,
            'polls' => $polls,
            'coredes' => $coredes,
            'citys' => $citys,
            'allCitys' => $allCitys,
            'filter' => $filter,
        );
    }
    /**
     * @Route("/rl/votos-csv", name="vpr_rl_votos_csv")
     */
    public function missioVotosCsvAction(Request $request)
    {
        $filter1 = $this->get('session')->get('filter_missio');
        if (isset($filter1['poll_id'])) {
            $em = $this->getDoctrine()->getManager();
            /* @var $rlCriterioRepo RlCriterioRepository */
            $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
            $entities = $rlCriterioRepo->findEspecial3($filter1)->fetchAll(\PDO::FETCH_ASSOC);
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="eleitores_municipio_'.$filter1['poll_id'] .'.csv";'
                );
            $response->sendHeaders();
    
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('COREDE_ID'
                , 'COREDE_NOME'
                , 'MUNICIPIOS_ID'
                , 'MUNICIPIOS_NOME'
                , 'MUNICIPIOS_IBGE'
                , 'PROGRAMA_ID'
                , 'PROGRAMA_NOME'
                , 'TOTAL_VOTOS'
                , 'PERCENTUAL_DO_NUMERO_DE_VOTOS'
                , 'PERCENTUAL_CORTE'
                , 'STATUS'
            ), $sep);
            foreach ($entities as $linha) {
                fputcsv($output, array($linha['corede_id']
                    , utf8_decode($linha['corede_name'])
                    , $linha['city_id']
                    , utf8_decode($linha['city_name'])
                    , $linha['ibge_code']
                    , $linha['option_id']
                    , utf8_decode($linha['option_name'])
                    , $linha['tot_in_city']
                    , $linha['perc_in_corede']
                    , $linha['perc_prog']
                    , utf8_decode($linha['status_prog_classificados'])
                ), $sep);
            }
            return $response;
        }
    }
    
    /**
     * @Route("/rl/programas-valor", name="vpr_rl_programas_valor")
     * @Template()
     */
    public function missioProgramasValorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo RlCriterioRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
        /* @var $coredeRepo Corede */
        $coredeRepo = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
        $filter = $this->montaFilterMissio();
        $filter1 = $this->get('session')->get('filter_missio');
        if ($request->isMethod('POST')) {
            $filter['poll_id'] = $request->get('poll_id');
            $filter['corede_id'] = $request->get('corede_id');
            $entities1 = $rlCriterioRepo->findEspecial4($filter)->fetchAll(\PDO::FETCH_ASSOC);
            $this->get('session')->set('filter_missio', $filter);
        } elseif ($filter1) {
            $filter = $filter1;
            $entities1 = $rlCriterioRepo->findEspecial4($filter)->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $entities1 = array();
        }
        $polls = $pollRepo->findBy(array(),array('openingTime' => 'desc'));
        $coredes = $coredeRepo->findBy(array(),array('name' => 'asc'));
        return array(
            'entities1' => $entities1,
            'polls' => $polls,
            'coredes' => $coredes,
            'filter' => $filter,
        );
    }
    /**
     * @Route("/rl/programas-valor-csv", name="vpr_rl_programas_valor_csv")
     */
    public function missioProgramasValorCsvAction(Request $request)
    {
        $filter1 = $this->get('session')->get('filter_missio');
        if (isset($filter1['poll_id'])) {
            $em = $this->getDoctrine()->getManager();
            /* @var $rlCriterioRepo RlCriterioRepository */
            $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
            $entities = $rlCriterioRepo->findEspecial4($filter1)->fetchAll(\PDO::FETCH_ASSOC);
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="programas_valor_'.$filter1['poll_id'] .'.csv";'
                );
            $response->sendHeaders();
    
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array('COREDE_ID'
                , 'COREDE_NOME'
                , 'PROGRAMA_ID'
                , 'PROGRAMA_NOME'
                , 'SECRETARIA_ID'
                , 'SECRETARIA_NOME'
                , 'VOTOS'
                , 'N_CLASSIFICADOS'
                , 'CLASSIFICADO'
                , 'VALOR'
            ), $sep);
            foreach ($entities as $linha) {
                fputcsv($output, array($linha['corede_id']
                    , utf8_decode($linha['corede_name'])
                    , $linha['option_id']
                    , utf8_decode($linha['option_name'])
                    , $linha['rl_agency_id']
                    , utf8_decode($linha['rl_agency_name'])
                    , $linha['tot_corede']
                    , $linha['rank_in_corede']
                    , utf8_decode($linha['classificado'])
                    , number_format($linha['tot_value_calc'], 2, ',', '.')
                ), $sep);
            }
            return $response;
        }
    }
    /**
     * @Route("/rl/resumo-programas-valor", name="vpr_rl_resumo_programas_valor")
     * @Template()
     */
    public function missioResumoProgramasValorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        /* @var $rlCriterioRepo RlCriterioRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
        
        $filter = $this->montaFilterMissio();
        $filter1 = $this->get('session')->get('filter_missio');
        if ($request->isMethod('POST')) {
            $filter['poll_id'] = $request->get('poll_id');
            $entities1 = $rlCriterioRepo->findEspecial5($filter);
            $this->get('session')->set('filter_missio', $filter);
        } elseif ($filter1) {
            $filter = $filter1;
            $entities1 = $rlCriterioRepo->findEspecial5($filter);
        } else {
            $entities1 = array();
        }
        $polls = $pollRepo->findBy(array(),array('openingTime' => 'desc'));
        return array(
            'entities1' => $entities1,
            'polls' => $polls,
            'filter' => $filter,
        );
    }
    /**
     * @Route("/rl/resumo-programas-valor-csv", name="vpr_rl_resumo_programas_valor_csv")
     */
    public function missioResumoProgramasValorCsvAction(Request $request)
    {
        $filter1 = $this->get('session')->get('filter_missio');
        if (isset($filter1['poll_id'])) {
            $em = $this->getDoctrine()->getManager();
            /* @var $rlCriterioRepo RlCriterioRepository */
            $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
            $entities = $rlCriterioRepo->findEspecial5($filter1);
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="resumo_programa_'.$filter1['poll_id'] .'.csv";'
                );
            $response->sendHeaders();
    
            $output = fopen('php://output', 'w');
            $sep = ';';
            fputcsv($output, array(
                'SECRETARIA_ID'
                , 'SECRETARIA_NOME'
                , 'PROGRAMA_NOME'
                , 'VALOR'
            ), $sep);
            foreach ($entities as $linha) {
                fputcsv($output, array($linha['rl_agency_id']
                    , utf8_decode($linha['rl_agency_name'])
                    , utf8_decode($linha['option_name'])
                    , number_format($linha['tot_value_calc'], 2, ',', '.')
                ), $sep);
            }
            return $response;
        }
    }
    private function montaFilterMissio()
    {
        $filter['poll_id'] = null;
        $filter['corede_id'] = null;
        $filter['city_id'] = null;
        return $filter;
    }
    private function resumoCoredeEntities($filter)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $rlCriterioRepo RlCriterioRepository */
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
        $filter1 = $filter;
        $filter1['classificado'] = 1;
        $entities1 = $rlCriterioRepo->findEspecial4($filter1)->fetchAll(\PDO::FETCH_GROUP);
        $entities2 = $rlCriterioRepo->findEspecial3($filter)->fetchAll(\PDO::FETCH_GROUP);
        $entities3 = array();
        $entities4 = array();
        foreach ($entities1 as $key1 => $val1) {
            $total = 0;
            $total2 = 0;
            $item = array();
            foreach ($val1 as $val2) {
                $total += $val2['tot_value_calc'];
                $total2++;
            }
            $item['corede_id'] = $key1;
            $item['corede_name'] = $val1[0]['corede_name'];
            $item['total'] = $total;
            $item['total_cols'] = $total2;
            $entities3[$key1] = $item;
        }
        foreach ($entities2 as $key1 => $val1) {
            $item = array();
            $first = $val1[0];
            $currentCoredeId1 = $first['corede_id'];
            $item['city_id'] = $key1;
            $item['city_name'] = $first['city_name'];
            $item['status_corte_mun'] = $first['status_corte_mun'];
            $item['total_cols'] = $entities3[$currentCoredeId1]['total_cols']; 
            $currentTotalCols = $item['total_cols'];
            $item['cols'] = array();
            foreach ($entities3 as $key3 => $val3) {
                if ($key3 == $currentCoredeId1) {
                    $a = array();
                    foreach ($val1 as $val2) {                        
                        if ($val2['rank_in_corede'] <= $currentTotalCols) {
                            $a[$val2['rank_in_corede']*1] = array(
                                'program' => $val2['tot_in_city'], 
                                'status_program' => $val2['status_combinado_prog_classificados']
                            );
                        }
                    }
                    for ($i =1; $i <= $currentTotalCols; $i++) {
                        if (!isset($a[$i])) {
                            $a[$i] = array('program' => null, 'status_program' => null);
                        }
                    }
                    ksort($a);
                    foreach ($a as $b) {
                        $item['cols'][] = $b;
                    }
                } else {
                    for ($i =0; $i< $val3['total_cols']; $i++) {
                        $item['cols'][] = array('program' => null, 'status_program' => null);
                    }
                }
            }
            $entities4[$key1] = $item;
            unset($entities2[$key1]);
        }
        return array($entities1, $entities3, $entities4);
    }
    /**
     * @Route("/rl/resumo-corede", name="vpr_rl_resumo_corede")
     * @Template()
     */
    public function missioResumoCoredeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $pollRepo PollRepository */
        $pollRepo = $em->getRepository('PROCERGSVPRCoreBundle:Poll');
        $rlCriterioRepo = $em->getRepository('PROCERGSVPRCoreBundle:RlCriterio');
        /* @var $coredeRepo Corede */
        $coredeRepo = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
        /* @var $cityRepo CityRepository */
        $cityRepo = $em->getRepository('PROCERGSVPRCoreBundle:City');
        $filter = $this->montaFilterMissio();
        $filter1 = $this->get('session')->get('filter_missio');
        if ($request->isMethod('POST')) {
            $filter['poll_id'] = $request->get('poll_id');
            $filter['corede_id'] = $request->get('corede_id');
            $filter['city_id'] = $request->get('city_id');
            list($entities1, $entities3, $entities4) = $this->resumoCoredeEntities($filter);
            $this->get('session')->set('filter_missio', $filter);
        } elseif ($filter1) {
            $filter = $filter1;
            list($entities1, $entities3, $entities4) = $this->resumoCoredeEntities($filter);
        } else {
            $entities1 = array();
            $entities3 = array();
            $entities4 = array();
        }
        $polls = $pollRepo->findBy(array(),array('openingTime' => 'desc'));
        $coredes = $coredeRepo->findBy(array(),array('name' => 'asc'));
        if ($filter['corede_id']) {
            $citys = $cityRepo->findCombo1($filter['corede_id']);
        } else {
            $citys = array();
        }
        $allCitys = $cityRepo->findCombo1();        
        return array(
            'entities1' => $entities1,
            'entities3' => $entities3,
            'entities4' => $entities4,
            'polls' => $polls,
            'coredes' => $coredes,
            'citys' => $citys,
            'allCitys' => $allCitys,
            'filter' => $filter,
        );
    }
    /**
     * @Route("/rl/resumo-corede-csv", name="vpr_rl_resumo_corede_csv")
     */
    public function missioResumoCoredeCsvAction(Request $request)
    {
        $filter1 = $this->get('session')->get('filter_missio');
        if (isset($filter1['poll_id'])) {
            list($entities1, $entities3, $entities4) = $this->resumoCoredeEntities($filter1);
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'application/vnd.oasis.opendocument.spreadsheet');
            $response->headers->set(
                'Content-Disposition',
                'attachment; filename="resumo_corede_'.$filter1['poll_id'] .'.ods";'
                );
            $response->sendHeaders();
            die(utf8_decode($this->renderView('PROCERGSVPRCoreBundle::Stats/missioResumoCoredeList.html.twig', array(
                'entities1' => ($entities1),
                'entities3' => ($entities3),
                'entities4' => ($entities4),
            ))));
        }
    }
}
