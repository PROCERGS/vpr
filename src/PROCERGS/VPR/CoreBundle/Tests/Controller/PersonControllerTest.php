<?php

namespace PROCERGS\VPR\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonControllerTest extends WebTestCase
{
    public function testSetcity()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/city/select');
    }

}
