<?php

namespace PROCERGS\VPR\CoreBundle\Service;


use Circle\RestClientBundle\Services\RestClient;
use PROCERGS\VPR\CoreBundle\Entity\Sms\PhoneNumber;
use PROCERGS\VPR\CoreBundle\Entity\Sms\Sms;
use PROCERGS\VPR\CoreBundle\Exception\SmsServiceException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class SmsService implements LoggerAwareInterface
{
    /** @var RestClient */
    protected $restClient;

    /** @var string */
    protected $sendUrl;

    /** @var string */
    protected $receiveUrl;

    /** @var string */
    protected $statusUrl;

    /** @var string */
    protected $serviceOrder;

    /** @var string */
    protected $systemId;

    /** @var string */
    protected $systemKey;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * SmsService constructor.
     * @param RestClient $restClient
     * @param array $options
     */
    public function __construct(RestClient $restClient, array $options = [])
    {
        $this->restClient = $restClient;

        $this->sendUrl = $options['send_url'];
        $this->receiveUrl = $options['receive_url'];
        $this->statusUrl = $options['status_url'];
        $this->systemId = $options['system_id'];
        $this->serviceOrder = $options['service_order'];

        if (array_key_exists('authentication', $options)) {
            $auth = $options['authentication'];
            if (array_key_exists('system_id', $auth)) {
                $this->systemId = $auth['system_id'];
            }
            if (array_key_exists('system_key', $auth)) {
                $this->systemKey = $auth['system_key'];
            }
        }
    }

    /**
     * Sends an SMS and returns the id for later checking.
     * @param Sms $sms
     * @return string transaction id for later checking
     * @throws SmsServiceException
     */
    public function send(Sms $sms)
    {
        $client = $this->restClient;

        $payload = json_encode(
            [
                'aplicacao' => $this->systemId,
                'ordemServico' => $this->serviceOrder,
                'remetente' => $sms->getFrom(),
                'texto' => $sms->getMessage(),
                'ddd' => $sms->getTo()->getAreaCode(),
                'numero' => $sms->getTo()->getSubscriberNumber(),
            ]
        );

        $this->logger->info("Sending SMS to {$sms->getTo()->toE164()}: {$sms->getMessage()}");
        $response = $client->post($this->sendUrl, $payload);
        $json = json_decode($response->getContent());
        if ($response->isOk() && property_exists($json, 'protocolo')) {
            $this->logger->info("SMS sent to {$sms->getTo()->toE164()}: {$sms->getMessage()}");

            return $json->protocolo;
        } else {
            $this->logger->error("Error sending log to {$sms->getTo()->toE164()}");
            $this->handleException($response, $json);
        }
    }

    /**
     * Force fetch pending SMS messages
     * @param $tag string "tag" to be fetched
     * @param integer $lastId
     * @return array
     * @throws SmsServiceException
     */
    public function forceReceive($tag, $lastId = null)
    {
        $client = $this->restClient;

        $params = compact('tag');
        if ($lastId !== null) {
            $params['ultimoId'] = $lastId;
        }

        $this->logger->info("Fetching SMS for tag $tag...");
        $response = $client->get($this->receiveUrl."?".http_build_query($params));
        $json = json_decode($response->getContent());
        if ($response->isOk() && $json !== null && is_array($json)) {
            usort(
                $json,
                function ($a, $b) {
                    return $a->id - $b->id;
                }
            );

            return $json;
        } else {
            $this->handleException($response, $json);
        }
    }

    /**
     * @param Response $response
     * @param mixed $json
     * @throws SmsServiceException
     */
    private function handleException(Response $response, $json = null)
    {
        if ($json === null) {
            $json = json_decode($response->getContent());
        }
        if (is_array($json)) {
            throw new SmsServiceException($json, $response->getStatusCode());
        } else {
            throw new SmsServiceException($response->getContent(), $response->getStatusCode());
        }
    }

    /**
     * Checks the status of sent messages
     * @param $transactionId mixed
     * @return array
     * @throws SmsServiceException
     */
    public function getStatus($transactionId)
    {
        if (is_array($transactionId)) {
            $transactionIds = $transactionId;
        } else {
            $transactionIds = [$transactionId];
        }

        $client = $this->restClient;
        $response = $client->get($this->statusUrl.'?protocolos='.implode(',', $transactionIds));
        $json = json_decode($response->getContent());
        if ($response->isOk() && $json !== null && is_array($json)) {
            return $json;
        } else {
            $this->handleException($response, $json);
        }
    }

    /**
     * @param PhoneNumber $to
     * @param string $message
     * @return string
     */
    public function easySend(PhoneNumber $to, $message)
    {
        $sms = new Sms();
        $sms
            ->setFrom($this->systemId)
            ->setTo($to)
            ->setMessage($message);

        return $this->send($sms);
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
