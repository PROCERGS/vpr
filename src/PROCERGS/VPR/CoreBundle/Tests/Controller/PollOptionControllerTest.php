<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PollOptionControllerTest extends WebTestCase
{
    public function testViewbycity()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewByCity');
    }

}
