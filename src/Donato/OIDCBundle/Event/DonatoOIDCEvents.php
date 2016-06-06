<?php

namespace Donato\OIDCBundle\Event;

class DonatoOIDCEvents
{
    /**
     * Receives a FilterRequestEvent
     */
    const OIDC_BEFORE_CALLBACK = 'donato.oidc.event.before_callback';

    /**
     * Receives a FilterOIDCClientEvent
     */
    const OIDC_BEFORE_AUTH = 'donato.oidc.event.before_auth';

    /**
     * Receives a FilterOIDCTokenEvent
     */
    const OIDC_FILTER_TOKEN = 'donato.oidc.event.filter_token';

    /**
     * Receives a FilterResponseEvent
     */
    const OIDC_FILTER_RESPONSE = 'donato.oidc.event.filter_response';
}