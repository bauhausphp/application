<?php

namespace Bauhaus;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApplicationTest extends TestCase
{
    private $application;

    protected function setUp()
    {
        $this->application = new Application();
    }

    /**
     * @test
     */
    public function stackUpPsr15Middleware()
    {
        $middleware = new PassMiddleware();
        $expectedStack = [$middleware];

        $this->application->stackUp($middleware);

        $this->assertEquals($expectedStack, $this->application->stack());
    }

    /**
     * @test
     */
    public function stackUpPsr15MiddlewareFromString()
    {
        $this->application->stackUp(PassMiddleware::class);
        $expectedStack = [PassMiddleware::class];

        $this->assertEquals($expectedStack, $this->application->stack());
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
        $this->application->stackUp($notPsr15Middleware);
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
     * @dataProvider applicationsThatReachTheGroundDelegator
     * @expectedException \Bauhaus\GroundDelegatorReachedException
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
}
