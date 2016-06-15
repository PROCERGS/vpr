<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmsVoteControllerTest extends WebTestCase
{
    public function testReceive()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/receive');
    }

}
