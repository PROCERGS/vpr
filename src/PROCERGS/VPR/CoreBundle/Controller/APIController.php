<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use PROCERGS\VPR\CoreBundle\Exception\RequestTimeoutException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class APIController extends FOSRestController
{

    /**
     * @REST\Get("/wait/person/update", name="procergsvpr_core_end_lc_query")
     * @REST\View
     */
    public function waitPersonChangeAction()
    {
        $accessToken = $this->getUser()->getLoginCidadaoAccessToken();
        $parameters = http_build_query(array(
            'access_token' => $accessToken,
            'updated_at' => '2014-05-25 00:00:00'
        ));

        $url = $this->container->getParameter('login_cidadao_base_url');
        $url .= "/api/v1/wait/person/update?$parameters";

        $browser = $this->get('buzz.browser');
        $callback = $this->getWaitPersonCallback($browser, $url);
        $person = $this->runTimeLimited($callback);

        $personArray = $this->objectToArray($person);
        $view = $this->view()->setData($personArray);
        
        return $this->handleView($view);
    }

    private function runTimeLimited($callback, $waitTime = 1)
    {
        $maxExecutionTime = ini_get('max_execution_time');
        $limit = $maxExecutionTime ? $maxExecutionTime - 2 : 60;
        $startTime = time();
        while ($limit > 0) {
            $result = call_user_func($callback);
            $delta = time() - $startTime;

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
                    return ($response !== false && $receivedPerson) ? $receivedPerson : false;
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

}
