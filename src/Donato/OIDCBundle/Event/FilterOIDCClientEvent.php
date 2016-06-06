<?php
/**
 * Created by PhpStorm.
 * User: gdnt
 * Date: 14/03/16
 * Time: 15:22
 */

namespace Donato\OIDCBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class FilterOIDCClientEvent extends Event
{
    /** @var \OpenIDConnectClient */
    protected $oidc;

    public function __construct(\OpenIDConnectClient $oidc)
    {
        $this->oidc = $oidc;
    }

    /**
     * @return \OpenIDConnectClient
     */
    public function getOpenIDConnectClient()
    {
        return $this->oidc;
    }

    /**
     * @param \OpenIDConnectClient $oidc
     * @return FilterOIDCClientEvent
     */
    public function setOpenIDConnectClient($oidc)
    {
        $this->oidc = $oidc;

        return $this;
    }
}
