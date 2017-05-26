<?php

namespace Bauhaus;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Bauhaus\Middlewares\PassMiddleware;
use Bauhaus\Middlewares\NotPsr15Middleware;
use Bauhaus\Middlewares\FixedResponseMiddleware;

class ApplicationTest extends TestCase
{
    /**
     * @test
     * @dataProvider applicationsThatReturnFixedResponse
     */
    public function handleServerRequestDelegatingItToTheMiddlewareChain(
        Application $application,
        ResponseInterface $expectedResponse
    ) {
        $serverRequest = $this->createMock(ServerRequestInterface::class);

        $response = $application->handle($serverRequest);

        $this->assertSame($expectedResponse, $response);
    }

    public function applicationsThatReturnFixedResponse(): array
    {
        $responseOne = $this->createMock(ResponseInterface::class);
        $responseTwo = $this->createMock(ResponseInterface::class);

        $passMiddleware = new PassMiddleware();
        $fixedResponseMiddlewareOne = new FixedResponseMiddleware($responseOne);
        $fixedResponseMiddlewareTwo = new FixedResponseMiddleware($responseTwo);

        $applicationOne = new Application();
        $applicationOne->stackUp($fixedResponseMiddlewareOne);
        $applicationOne->stackUp($passMiddleware);

        $applicationTwo = new Application();
        $applicationTwo->stackUp($fixedResponseMiddlewareOne);
        $applicationTwo->stackUp($fixedResponseMiddlewareTwo);
        $applicationTwo->stackUp($passMiddleware);

        return [
            [$applicationOne, $responseOne],
            [$applicationTwo, $responseTwo],
        ];
    }

    /**
     * @test
     * @dataProvider applicationsThatReachTheGroundDelegator
     * @expectedException \Bauhaus\Application\GroundDelegatorReachedException
     * @expectedExceptionMessage Ground delegator reached
     */
    public function exceptionOccursWhenEveryMiddlewareStackedDelegateTheProcess(
        Application $application
    ) {
        $serverRequest = $this->createMock(ServerRequestInterface::class);

        $application->handle($serverRequest);
    }

    public function applicationsThatReachTheGroundDelegator(): array
    {
        $emptyStackApp = new Application();

        $onlyPassMiddlewareApp = new Application();
        $onlyPassMiddlewareApp->stackUp(new PassMiddleware());
        $onlyPassMiddlewareApp->stackUp(new PassMiddleware());

        return [
            [$emptyStackApp],
            [$onlyPassMiddlewareApp],
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
        $application = new Application();

        $application->stackUp($notPsr15Middleware);
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

        $application = new Application($diContainer);
        $application->stackUp(FixedResponseMiddleware::class);

        $result = $application->handle($serverRequest);

        $this->assertSame($response, $result);
    }
}
