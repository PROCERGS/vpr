<?php

namespace Donato\OIDCBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testAskprovider()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/askProvider');
    }

}
