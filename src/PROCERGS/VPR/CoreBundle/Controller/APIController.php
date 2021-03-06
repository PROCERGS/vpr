<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use PROCERGS\VPR\CoreBundle\Exception\RequestTimeoutException;
use PROCERGS\VPR\CoreBundle\Entity\Vote;
use PROCERGS\VPR\CoreBundle\Entity\PollOption;
use JMS\Serializer\SerializationContext;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Rhumsaa\Uuid\Uuid;

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
        $em = $this->getDoctrine()->getManager();
        if ($pin == 1) {
        	$ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')
        	->findByPinAndPollFilteredByCorede($pin);
        	$passphrase = $request->get('passphrase', null);
        	$privateKey = openssl_pkey_get_private($ballotBox->getPrivateKey(),
        			$passphrase);
        	if ($privateKey === false) {
        		return new JsonResponse(array('code' => 403, 'message' => "Senha inválida."), 403);
        	}
        } else {
	        $ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')
        	->findByPinAndPollFilteredByCorede($pin);
	        if (!$ballotBox) {
	        	return new JsonResponse(array('code' => 403, 'message' => "Pin informado não existe."), 403);
	        }
	        if ($ballotBox->getSetupAt() instanceof \DateTime) {
	        	return new JsonResponse(array('code' => 403, 'message' => "Não é possivel utilizar essa Urna, pois ela ja foi utilizada."), 403);
	        }
	        if ($ballotBox->getClosedAt() instanceof \DateTime) {
	        	return new JsonResponse(array('code' => 403, 'message' => "Não é possivel utilizar essa Urna, pois ela ja foi utilizada e transmitida."), 403);
	        }	
	        $passphrase = $request->get('passphrase', null);
	        $privateKey = openssl_pkey_get_private($ballotBox->getPrivateKey(),
	            $passphrase);
	        if ($privateKey === false) {
	        	return new JsonResponse(array('code' => 403, 'message' => "Senha inválida"), 403);
	        }
	
	        $ballotBox->setSetupAt(new \DateTime());
	        $em->persist($ballotBox);
	        $em->flush();
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
        $this->getLogger()->info("Receiving votes from PIN $pin...");
        if ($pin == 1) {
        	return new JsonResponse(array(
        			'hash' => true,
        	));
        }
        $em        = $this->getDoctrine()->getManager();
        $ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')
            ->findOneByPin($pin);
		if (!$ballotBox) {
			return new JsonResponse(array('code' => 403, 'message' => "Pin informado não existe."), 403);
		}
        if ($ballotBox->getClosedAt() instanceof \DateTime) {
        	return new JsonResponse(array('code' => 403, 'message' => "Urna ja foi transmitida."), 403);
        }

        $logger = $this->getLogger();
        $votes  = $request->get('votes');
        $hash   = $request->get('hash');

        $secret     = $ballotBox->getSecret();
        $calculated = base64_encode(hash_hmac('sha256', $votes, $secret, true));

        $logger->debug($request->getClientIp());
        $this->checkHash($hash, $calculated, $logger);

        $ballotBox->setCsv($votes);
        $ballotBox->setClosedAt(new \DateTime());
        $em->persist($ballotBox);
        $em->flush();

        return new JsonResponse(array(
            'hash' => true,
        ));
    }
    
    /**
     * @REST\Get("/api/status", name="vpr_api_ping")
     * @REST\View
     */
    public function statusAction()
    {
        $status = 200;
        $tests  = array(
            'database' => $this->checkStatusDatabase(),
            'filesystem' => true
        );

        foreach ($tests as $test => $value) {
            if ($value !== true) {
                $status = 500;
                $this->getLogger()->err("$test: $value", array('API Status'));
            }
        }

        return new JsonResponse(array(
            'status' => $tests,
            'hostname' => php_uname('n'),
            'timestamp' => new \DateTime()
            ), $status);
    }

    /**
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
        if ($vote->getBallotBox() === null) {
            throw new BadRequestHttpException('Missing ballot_box');
        }
    }

    private function validateVoteOptions(Vote $vote)
    {
        $em        = $this->getDoctrine()->getManager();
        $ballotBox = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')
            ->find($vote->getBallotBox()->getId());

        $options = $vote->getOptions();
        if ($vote->getSignature() === null) {
            $this->getLogger()->crit('DISABLE THIS IN PRODUCTION!');
        } else {
            $signature = base64_decode($vote->getSignature());
            $publicKey = openssl_pkey_get_public($ballotBox->getPublicKey());
            $check     = openssl_verify($options, $signature, $publicKey);
            if ($check !== 1) {
                throw new BadRequestHttpException('Invalid signature! '.$vote->getSignature());
            }
        }
        if ($vote->getPassphrase() === null) {
            $this->validateUnencryptedOptions($options);
        }
        return true;
    }

    public function validateUnencryptedOptions($json)
    {
        try {
            $serializer = $this->getJmsSerializer();
            $options    = $serializer->deserialize($json,
                'ArrayCollection<PROCERGS\VPR\CoreBundle\Entity\PollOption>',
                'json');
        } catch (\Exception $e) {
            $message = 'Invalid options: '.$json;
            throw new BadRequestHttpException($message, $e, 400);
        }

        foreach ($options as $option) {
            if (!($option instanceof PollOption)) {
                throw new BadRequestHttpException('Invalid poll option.');
            }
        }
    }

    private function checkStatusDatabase()
    {
        try {
            $this->getDoctrine()->getManager()
                ->getRepository('PROCERGSVPRCoreBundle:Poll')
                ->findLastPoll();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function checkStatusFilesystem()
    {
        try {
            $fs       = $this->getVotesDumpFs();
            $content  = rand(0, 99999);
            $filename = 'status.'.rand(0, 99999);
            $fs->write($filename, $content);

            if ($fs->read($filename) != $content) {
                throw new \RuntimeException('Unable to write votes');
            }
            $fs->delete($filename);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
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

    /**
     * @return \Gaufrette\Filesystem
     */
    private function getVotesDumpFs()
    {
        return $this->get('knp_gaufrette.filesystem_map')->get('votes_dump_fs');
    }

    /**
     * @REST\Get("/api/offline/download/{pin}", name="vpr_api_download_offline_votes", defaults={"pin" = ""})
     * @REST\View
     */
    public function downloadOfflineVotesAction($pin = '')
    {
        $zipName     = "offline.".($pin !== '' ? "$pin." : 'all.').Uuid::uuid4().'.zip';
        $zipFilename = realpath('../uploads').DIRECTORY_SEPARATOR.$zipName;

        $fs   = $this->getVotesDumpFs();
        $keys = $fs->listKeys('ballot_box.'.$pin);

        $zipAdapter = new \Gaufrette\Adapter\Zip($zipFilename);
        $zipFs      = new \Gaufrette\Filesystem($zipAdapter);

        foreach ($keys['keys'] as $key) {
            $origin = $fs->get($key);
            $dest   = $zipFs->createFile($origin->getKey());
            $dest->setContent($origin->getContent());
        }

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($zipFilename));
        $response->headers->set('Content-Disposition',
            'attachment; filename="'.basename($zipFilename).'";');
        $response->headers->set('Content-length', filesize($zipFilename));

        $response->sendHeaders();

        $response->setContent(readfile($zipFilename));
        return $response;
    }
    
    /**
     * @REST\Get("/uploads/{pin}", name="vpr_uploads")
     * @REST\View
     */
    public function uploadsAction($pin)
    {
        $zipFilename = realpath('../uploads').DIRECTORY_SEPARATOR.$pin;    
        
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($zipFilename));
        $response->headers->set('Content-Disposition',
            'attachment; filename="'.basename($zipFilename).'";');
        $response->headers->set('Content-length', filesize($zipFilename));
    
        $response->sendHeaders();
    
        $response->setContent(readfile($zipFilename));
        return $response;
    }

    private function parseHumanBytes($val)
    {
        $val  = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }
}
