<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testLock()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/lock');
    }

}
