<?php

namespace Bauhaus\MiddlewareChain;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class ChainTest extends TestCase
{
    /**
     * @test
     * @dataProvider chainsThatReturnFixedResponse
     */
    public function handleServerRequestDelegatingItToTheMiddlewareChain(
        Chain $chain,
        ResponseInterface $expectedResponse
    ) {
        $serverRequest = $this->createMock(ServerRequestInterface::class);

        $response = $chain->handle($serverRequest);

        $this->assertSame($expectedResponse, $response);
    }

    public function chainsThatReturnFixedResponse(): array
    {
        $responseOne = $this->createMock(ResponseInterface::class);
        $responseTwo = $this->createMock(ResponseInterface::class);

        $passMiddleware = new PassMiddleware();
        $fixedResponseMiddlewareOne = new FixedResponseMiddleware($responseOne);
        $fixedResponseMiddlewareTwo = new FixedResponseMiddleware($responseTwo);

        $chainOne = new Chain();
        $chainOne->stackUp($fixedResponseMiddlewareOne);
        $chainOne->stackUp($passMiddleware);

        $chainTwo = new Chain();
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
        $serverRequest = $this->createMock(ServerRequestInterface::class);

        $chain->handle($serverRequest);
    }

    public function chainsThatReachTheGroundDelegator(): array
    {
        $emptyStackChain = new Chain();

        $passMiddlewareChain = new Chain();
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
        $chain = new Chain();

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
        $response = $this->createMock(ResponseInterface::class);
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $fixedResponseMiddleware = new FixedResponseMiddleware($response);

        $diContainer = $this->createMock(ContainerInterface::class);
        $diContainer
            ->method('get')
            ->will($this->returnValue($fixedResponseMiddleware));

        $chain = new Chain($diContainer);
        $chain->stackUp(FixedResponseMiddleware::class);

        $result = $chain->handle($serverRequest);

        $this->assertSame($response, $result);
    }
}
