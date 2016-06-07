<?php
/**
 * Created by PhpStorm.
 * User: gdnt
 * Date: 14/03/16
 * Time: 15:22
 */

namespace Donato\OIDCBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class FilterRequestEvent extends Event
{
    /** @var Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return FilterRequestEvent
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}