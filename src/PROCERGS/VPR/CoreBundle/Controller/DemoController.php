<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\Serializer\SerializationContext;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Exception\RequestTimeoutException;

class DemoController extends Controller
{

    /**
     * @Route("/ballotbox/create")
     * @Template()
     */
    public function newBallotBoxAction()
    {
        $poll = $this->getPoll(1);

        $ballotBox = new BallotBox();
        $ballotBox->setName('Internet')
                ->setSecret(rand(100000, 9999999))
                ->setAddress('http://vpr.rs.gov.br/')
                ->setIsOnline(true)
                ->setPoll($poll);

        $em = $this->getDoctrine()->getManager();
        $em->persist($ballotBox);
        $em->flush();

        return compact('ballotBox', 'poll');
    }

    /**
     * @Route("/ballotbox/view")
     * @Template()
     */
    public function viewBallotBoxAction()
    {

    }

    /**
     * @param integer $id
     * @return \PROCERGS\VPR\CoreBundle\Entity\Poll
     */
    private function getPoll($id)
    {
        $polls = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:Poll');
        return $polls->find($id);
    }

    /**
     * @param integer $id
     * @return \PROCERGS\VPR\CoreBundle\Entity\BallotBox
     */
    private function getBallotBox($id)
    {
        $polls = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:BallotBox');
        return $polls->find($id);
    }

    /**
     * @Route("/vote/add/{ballotBoxId}")
     * @Template()
     */
    public function newVoteAction($ballotBoxId)
    {
        $serializer = $this->container->get('jms_serializer');

        $person = $this->getUser();

        $poll = $this->getPoll(1);
        $ballotBox = $this->getBallotBox($ballotBoxId);

        $optionsRepo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:PollOption');
        $options = $optionsRepo->findByPoll($poll);

        $serializedOptions = $serializer->serialize($options, 'json',
                SerializationContext::create()->setSerializeNull(true)->setGroups(array('vote')));

        $vote = new Vote();
        $vote->setAuthType('lc')
                ->setVoterRegistration($person->getVoterRegistration())
                ->setPlainOptions($serializedOptions)
                ->setBallotBox($ballotBox);

        $signature = $ballotBox->sign($serializedOptions);
        $vote->setSignature($signature)->encryptVote();
        $serializedVote = $serializer->serialize($vote, 'json',
                SerializationContext::create()->setSerializeNull(true)->setGroups(array('vote')));

        $privateKeyFile = $this->container->getParameter('privateKeyFile');
        $privatePollKey = openssl_pkey_get_private($privateKeyFile);

        $openVote = $serializer->deserialize($serializedVote,
                'PROCERGS\VPR\CoreBundle\Entity\Vote', 'json');
        $openVote->openVote($privatePollKey);

        $openOptions = $serializer->deserialize($openVote->getPlainOptions(),
                'ArrayCollection<PROCERGS\VPR\CoreBundle\Entity\PollOption>',
                'json');

        return compact('poll', 'options', 'serializedOptions', 'signature',
                'serializedVote', 'vote', 'openVote', 'openOptions');
    }

    /**
     * @Route("/long-polling", defaults={"_format" = "json"})
     * @Template()
     */
    public function longPollingAction()
    {
        $accessToken = $this->getUser()->getLoginCidadaoAccessToken();
        $url = $this->container->getParameter('login_cidadao_base_url');
        $url .= "/api/v1/wait/person/voter-registration?access_token=$accessToken";

        $client = $this->get('buzz.client');
        $response = $client->get($url);
        print_r($response);

        $person = $this->runTimeLimited(function() use ($url) {
            try {
                $response = @file_get_contents($url);
                $receivedPerson = json_decode($response);
                return ($response !== false && $receivedPerson) ? $receivedPerson : false;
            } catch (\Exception $e) {
                return false;
            }
        });

        $response = new JsonResponse();
        return $response->setData(json_decode($person));
    }

    private function runTimeLimited($callback, $waitTime = 1)
    {
        $limit = ini_get('max_execution_time') - 2;
        $startTime = time();
        while ($limit > 0) {
            $result = call_user_func($callback);
            $delta = time() - $startTime;

            if ($result !== false) {
                return $result;
            } else {
                $limit -= $delta;
                $startTime = time();
                sleep($waitTime);
            }
        }
        throw new RequestTimeoutException();
    }

}
