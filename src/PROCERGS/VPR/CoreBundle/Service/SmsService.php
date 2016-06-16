<?php

namespace PROCERGS\VPR\CoreBundle\Service;


use Circle\RestClientBundle\Services\RestClient;
use PROCERGS\VPR\CoreBundle\Entity\Sms\Sms;
use PROCERGS\VPR\CoreBundle\Exception\SmsServiceException;

class SmsService
{
    /** @var RestClient */
    protected $restClient;

    /** @var string */
    protected $url;

    /** @var string */
    protected $serviceOrder;

    /** @var string */
    protected $systemId;

    /** @var string */
    protected $systemKey;

    /**
     * SmsService constructor.
     * @param RestClient $restClient
     * @param array $options
     */
    public function __construct(RestClient $restClient, array $options = [])
    {
        $this->restClient = $restClient;

        if (array_key_exists('url', $options)) {
            $this->url = $options['url'];
        }

        if (array_key_exists('service_order', $options)) {
            $this->serviceOrder = $options['service_order'];
        }

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
     * @return string sending id for later checking
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
                'text' => $sms->getMessage(),
                'ddd' => $sms->getTo()->getAreaCode(),
                'numero' => $sms->getTo()->getSubscriberNumber(),
            ]
        );

        $response = $client->post($this->url, $payload);
        $json = json_decode($response->getContent());
        if ($response->isOk() && property_exists($json, 'protocolo')) {
            return $json->protocolo;
        } else {
            throw new SmsServiceException($json);
        }
    }
}
