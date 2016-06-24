<?php

namespace PROCERGS\VPR\CoreBundle\Service;

use Ejsmont\CircuitBreaker\Core\CircuitBreaker;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnosticsTest\ResultCollectionTest;

class SendSmsCircuitBreakerCheck implements CheckInterface
{

    /** @var CircuitBreaker */
    protected $circuitBreaker;

    /**
     * SendSmsCircuitBreakerCheck constructor.
     * @param CircuitBreaker $circuitBreaker
     */
    public function __construct(CircuitBreaker $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;
    }

    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check()
    {
        $result = new ResultCollectionTest();
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Check if SMS circuit breakers are open';
    }
}