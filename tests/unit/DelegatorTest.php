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
   public function shouldDelegateTheProcessToItsMiddleware()
    {
        $expectedResponse = $this->createMock(Response::class);
        $fixedResponseMiddleware = new FixedResponseMiddleware($expectedResponse);
        $dummyDelegator = $this->createMock(Delegate::class);
        $delegator = new Delegator($fixedResponseMiddleware, $dummyDelegator);
        $serverRequest = $this->createMock(ServerRequest::class);

        $response = $delegator->process($serverRequest);

        $this->assertSame($expectedResponse, $response);
    }
}
