<?php

namespace PROCERGS\VPR\CoreBundle\Tests;

require_once dirname(__DIR__).'/../../../../app/AppKernel.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\Container;

abstract class KernelAwareTest extends \PHPUnit_Framework_TestCase
{
    /** @var \AppKernel */
    protected $kernel;

    /** @var EntityManager */
    protected $em;

    /** @var Container */
    protected $container;

    public function setUp()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();

        $this->generateSchema();

        parent::setUp();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();

        parent::tearDown();
    }

    protected function generateSchema()
    {
        $metadata = $this->getMetadata();

        if (!empty($metadata)) {
            $tool = new SchemaTool($this->em);
            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);
        }
    }

    /**
     * @return array
     */
    protected function getMetadata()
    {
        return $this->em->getMetadataFactory()->getAllMetadata();
    }
}
