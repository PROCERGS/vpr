<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testNewballotbox()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ballotbox/create');
    }

    public function testViewballotbox()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ballotbox/view');
    }

}
