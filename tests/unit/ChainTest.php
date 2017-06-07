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
        $responseThree = $this->createMock(Response::class);
        $passMiddleware = new PassMiddleware();
        $fixedResponseMiddlewareOne = new FixedResponseMiddleware($responseOne);
        $fixedResponseMiddlewareTwo = new FixedResponseMiddleware($responseTwo);
        $fixedResponseMiddlewareThree = new FixedResponseMiddleware($responseThree);

        $chainOne = Chain::create();
        $chainOne->stackUp($fixedResponseMiddlewareOne);

        $chainTwo = Chain::create();
        $chainTwo->stackUp($fixedResponseMiddlewareTwo);
        $chainTwo->stackUp($passMiddleware);

        $chainThree = Chain::create();
        $chainThree->stackUp($fixedResponseMiddlewareOne);
        $chainThree->stackUp($fixedResponseMiddlewareThree);
        $chainThree->stackUp($passMiddleware);

        return [
            [$chainOne, $responseOne],
            [$chainTwo, $responseTwo],
            [$chainThree, $responseThree],
        ];
    }

    /**
     * @test
     * @expectedException \Bauhaus\MiddlewareChain\GroundDelegatorReachedException
     * @expectedExceptionMessage Ground delegator reached
     */
    public function exceptionOccursWhenTryToHandleServerRequestWithAnEmptyChain()
    {
        $emptyChain = Chain::create();

        $serverRequest = $this->createMock(ServerRequest::class);

        $emptyChain->handle($serverRequest);
    }

    /**
     * @test
     * @expectedException \Bauhaus\MiddlewareChain\GroundDelegatorReachedException
     * @expectedExceptionMessage Ground delegator reached
     */
    public function exceptionOccursWhenEveryStackedMiddlewareDelegate()
    {
        $onlyPassMiddlewareChain = Chain::create();
        $onlyPassMiddlewareChain->stackUp(new PassMiddleware());
        $onlyPassMiddlewareChain->stackUp(new PassMiddleware());

        $serverRequest = $this->createMock(ServerRequest::class);

        $onlyPassMiddlewareChain->handle($serverRequest);
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
    public function loadFromTheDiCotnainerMiddlewaresStackedUpWithString()
    {
        $expectedResponse = $this->createMock(Response::class);
        $serverRequest = $this->createMock(ServerRequest::class);
        $diContainer = $this->createMock(Container::class);
        $diContainer
            ->method('get')
            ->willReturn(new FixedResponseMiddleware($expectedResponse));

        $chain = Chain::createWithDiContainer($diContainer);
        $chain->stackUp(FixedResponseMiddleware::class);
        $response = $chain->handle($serverRequest);

        $this->assertSame($expectedResponse, $response);
    }
}
