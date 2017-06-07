<?php

namespace Bauhaus\MiddlewareChain;

use PHPUnit\Framework\TestCase;
use Interop\Http\ServerMiddleware\DelegateInterface as Delegate;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class DelegatorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDelegateToItsMiddleware()
    {
        $fixedResponse = $this->createMock(Response::class);
        $dummyDelegator = $this->createMock(Delegate::class);
        $serverRequest = $this->createMock(ServerRequest::class);
        $fixedResponseMiddleware = new FixedResponseMiddleware($fixedResponse);
        $delegator = new Delegator($fixedResponseMiddleware, $dummyDelegator);

        $response = $delegator->process($serverRequest);

        $this->assertSame($fixedResponse, $response);
    }
}
