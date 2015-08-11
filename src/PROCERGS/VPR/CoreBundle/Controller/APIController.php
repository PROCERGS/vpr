<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use PROCERGS\VPR\CoreBundle\Exception\RequestTimeoutException;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use JMS\Serializer\SerializationContext;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIController extends FOSRestController
{

    /**
     * @REST\Get("/wait/person/update", name="procergsvpr_core_end_lc_query")
     * @REST\View
     */
    public function waitPersonChangeAction()
    {
        $accessToken = $this->getUser()->getLoginCidadaoAccessToken();
        $parameters  = http_build_query(array(
            'access_token' => $accessToken,
            'updated_at' => '2014-05-25 00:00:00'
        ));

        $url = $this->container->getParameter('login_cidadao_base_url');
        $url .= "/api/v1/wait/person/update?$parameters";

        $browser  = $this->get('buzz.browser');
        $callback = $this->getWaitPersonCallback($browser, $url);
        $person   = $this->runTimeLimited($callback);

        $personArray = $this->objectToArray($person);
        $view        = $this->view()->setData($personArray);

        return $this->handleView($view);
    }

    private function runTimeLimited($callback, $waitTime = 1)
    {
        $maxExecutionTime = ini_get('max_execution_time');
        $limit            = $maxExecutionTime ? $maxExecutionTime - 2 : 60;
        $startTime        = time();
        while ($limit > 0) {
            $result = call_user_func($callback);
            $delta  = time() - $startTime;

            if ($result !== false) {
                return $result;
            }

            $limit -= $delta;
            if ($limit <= 0) {
                break;
            }
            $startTime = time();
            sleep($waitTime);
        }
        throw new RequestTimeoutException("Request Timeout");
    }

    private function getWaitPersonCallback(\Buzz\Browser $browser, $url)
    {
        return function() use ($url, $browser) {
            $response = $browser->get($url);
            switch ($response->getStatusCode()) {
                case 200:
                    $receivedPerson = json_decode($response->getContent());
                    return ($response !== false && $receivedPerson) ? $receivedPerson
                            : false;
                case 408:
                    return false;
                default:
                    throw new HttpException($response->getStatusCode(),
                    $response->getContent());
            }
            return false;
        };
    }

    /**
     * This is here to 'fix' JMS Serializer's bug that serializes stdClass as {}
     * @param mixed $d
     * @return array
     */
    private function objectToArray($d)
    {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return array_map(array($this, __FUNCTION__), $d);
        } else {
            // Return array
            return $d;
        }
    }

    /**
     * @REST\Post("/ballotbox/{pin}", name="procergsvpr_core_dump_ballotbox")
     * @REST\View
     */
    public function dumpBallotBoxAction(Request $request, $pin)
    {
        $poll = $this->getDoctrine()->getManager()
            ->getRepository('PROCERGSVPRCoreBundle:Poll')
            ->findLastPoll();

        $ballotBox = $this->getDoctrine()->getManager()
            ->getRepository('PROCERGSVPRCoreBundle:BallotBox')
            ->findByPinAndPollFilteredByCorede($poll, $pin);

        $passphrase = $request->get('passphrase', null);
        $privateKey = openssl_pkey_get_private($ballotBox->getPrivateKey(),
            $passphrase);
        if ($privateKey === false) {
            throw new AccessDeniedHttpException("Invalid credentials");
        }

        $context = SerializationContext::create()
            ->setSerializeNull(true)
            ->setGroups(array('setup'));

        $view = $this->view()->setData($ballotBox);
        $view->setSerializationContext($context);

        return $this->handleView($view);
    }

    /**
     * @REST\Post("/ballotbox/{pin}/votes", name="procergsvpr_core_receive_votes")
     * @REST\View
     */
    public function receiveVotesAction(Request $request, $pin)
    {
        $logger     = $this->getLogger();
        $votes      = $request->get('votes');
        $hash       = $request->get('hash');
        $total      = $request->get('total');
        $calculated = hash('sha512', $votes);

        $logger->debug($request->getClientIp());
        $this->checkHash($hash, $calculated, $logger);
        $logger->debug($votes);

        $serializer = $this->getJmsSerializer();
        $data       = $serializer->deserialize($votes,
            'ArrayCollection<PROCERGS\VPR\CoreBundle\Entity\Vote>', 'json');

        $logger->debug(print_r($data, true));
        foreach ($data as $vote) {
            $this->checkVote($vote);
        }
        if ($total !== null) {
            $logger->debug("Total expected votes: $total");
            $logger->debug("Actual votes count: ".count($data));
        }
        return new JsonResponse(array(
            'hash' => true,
            'votes' => count($data)
        ));
    }

    /**
     * @REST\Get("/api/status", name="vpr_api_ping")
     * @REST\View
     */
    public function pingAction()
    {
        $tests = array();

        try {
            $this->getDoctrine()->getManager()
                ->getRepository('PROCERGSVPRCoreBundle:Poll')
                ->findLastPoll();

            $tests['database'] = true;
        } catch (\Exception $e) {
            $tests['database'] = $e->getMessage();
        }

        try {
            throw new \RuntimeException('Not implemented yet');
            $tests['filesystem'] = true;
        } catch (\Exception $e) {
            $tests['filesystem'] = $e->getMessage();
        }

        return new JsonResponse(array(
            'status' => $tests,
            'timestamp' => new \DateTime()
        ));
    }

    /**
     *
     * @param type $hash
     * @param type $calculated
     * @param Logger $logger
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    private function checkHash($hash, $calculated, Logger $logger)
    {
        if ($hash !== $calculated) {
            $logger->debug("Received hash: $hash");
            $logger->debug("Calculated hash: $calculated");
            if ($hash !== null) {
                throw new BadRequestHttpException("Hash didn't match! Use SHA-512 to hash the 'votes' parameter");
            }
        } else {
            $logger->debug("Hash OK");
        }
    }

    private function checkVote(Vote $vote)
    {
        if ($vote->getId() === null) {
            throw new BadRequestHttpException('Missing vote id');
        }
        if ($vote->getOptions() === null) {
            throw new BadRequestHttpException('Missing options');
        }
        if ($vote->getAuthType() === null) {
            throw new BadRequestHttpException('Missing auth type');
        }
        if ($vote->getVoterRegistration() === null) {
            throw new BadRequestHttpException('Missing voter registration');
        }
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
     */
    private function getJmsSerializer()
    {
        return $this->get('jms_serializer');
    }

    /**
     * @return Logger
     */
    private function getLogger()
    {
        return $this->get('monolog.logger.api');
    }
}
