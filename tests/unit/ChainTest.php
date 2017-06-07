<?php

namespace Bauhaus\MiddlewareChain;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class ChainTest extends TestCase
{
    /**
     * @test
     * @dataProvider chainsThatReturnFixedResponse
     */
    public function handleServerRequestDelegatingItToTheMiddlewareChain(
        Chain $chain,
        Response $expectedResponse
    ) {
        $serverRequest = $this->createMock(ServerRequest::class);

        $response = $chain->handle($serverRequest);

        $this->assertSame($expectedResponse, $response);
    }

    public function chainsThatReturnFixedResponse(): array
    {
        $responseOne = $this->createMock(Response::class);
        $responseTwo = $this->createMock(Response::class);

        $passMiddleware = new PassMiddleware();
        $fixedResponseMiddlewareOne = new FixedResponseMiddleware($responseOne);
        $fixedResponseMiddlewareTwo = new FixedResponseMiddleware($responseTwo);

        $chainOne = Chain::create();
        $chainOne->stackUp($fixedResponseMiddlewareOne);
        $chainOne->stackUp($passMiddleware);

        $chainTwo = Chain::create();
        $chainTwo->stackUp($fixedResponseMiddlewareOne);
        $chainTwo->stackUp($fixedResponseMiddlewareTwo);
        $chainTwo->stackUp($passMiddleware);

        return [
            [$chainOne, $responseOne],
            [$chainTwo, $responseTwo],
        ];
    }

    /**
     * @test
     * @dataProvider chainsThatReachTheGroundDelegator
     * @expectedException \Bauhaus\MiddlewareChain\GroundDelegatorReachedException
     * @expectedExceptionMessage Ground delegator reached
     */
    public function exceptionOccursWhenEveryStackedMiddlewareDelegateTheProcess(
        Chain $chain
    ) {
        $serverRequest = $this->createMock(ServerRequest::class);

        $chain->handle($serverRequest);
    }

    public function chainsThatReachTheGroundDelegator(): array
    {
        $emptyStackChain = Chain::create();

        $passMiddlewareChain = Chain::create();
        $passMiddlewareChain->stackUp(new PassMiddleware());
        $passMiddlewareChain->stackUp(new PassMiddleware());

        return [
            [$emptyStackChain],
            [$passMiddlewareChain],
        ];
    }

    /**
     * @test
     * @dataProvider notPsr15Middlewares
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Can only stack up PSR-15 middlewares
     */
    public function exceptionOccursWhenTryToStackUpANotPsr15Middleware(
        $notPsr15Middleware
    ) {
        $chain = Chain::create();

        $chain->stackUp($notPsr15Middleware);
    }

    public function notPsr15Middlewares()
    {
        return [
            [NotPsr15Middleware::class],
            [new NotPsr15Middleware()],
            ['some string'],
            [123],
        ];
    }

    /**
     * @test
     */
    public function middlewaresStackedUpWithStringAreLoadedFromDiCotnainer()
    {
        $response = $this->createMock(Response::class);
        $serverRequest = $this->createMock(ServerRequest::class);
        $fixedResponseMiddleware = new FixedResponseMiddleware($response);

        $diContainer = $this->createMock(Container::class);
        $diContainer
            ->method('get')
            ->will($this->returnValue($fixedResponseMiddleware));

        $chain = Chain::createWithDiContainer($diContainer);
        $chain->stackUp(FixedResponseMiddleware::class);

        $result = $chain->handle($serverRequest);

        $this->assertSame($response, $result);
    }
}
