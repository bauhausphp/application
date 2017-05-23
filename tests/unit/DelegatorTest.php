<?php

namespace Bauhaus;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

class DelegatorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDelegateToItsMiddleware()
    {
        $fixedResponse = $this->createMock(ResponseInterface::class);
        $dummyDelegator = $this->createMock(DelegateInterface::class);
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $fixedResponseMiddleware = new FixedResponseMiddleware($fixedResponse);
        $delegator = new Delegator($fixedResponseMiddleware, $dummyDelegator);

        $response = $delegator->process($serverRequest);

        $this->assertSame($fixedResponse, $response);
    }
}
