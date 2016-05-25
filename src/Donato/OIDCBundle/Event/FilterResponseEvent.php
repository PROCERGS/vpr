<?php
/**
 * Created by PhpStorm.
 * User: gdnt
 * Date: 14/03/16
 * Time: 15:43
 */

namespace Donato\OIDCBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class FilterResponseEvent extends Event
{
    /** @var Response */
    protected $response;

    /**
     * FilterResponseEvent constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return FilterResponseEvent
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }
}