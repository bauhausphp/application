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
     * @expectedExceptionMessage Only PSR-15 Middlewares can be stacked up
     */
    public function exceptionOccursWhenStackUpANotPsr15Middleware(
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
    public function exceptionOccursWhenAllMiddlewaresInStackDelegateTheProcess(
        Application $application
    ) {
        $serverRequest = $this->createMock(ServerRequestInterface::class);

        $application->process($serverRequest);
    }

    public function applicationsThatReachTheGroundDelegator(): array
    {
        $appWithEmptyStack = new Application();

        $appWithPassMiddleware = new Application();
        $appWithPassMiddleware->stackUp(new PassMiddleware());
        $appWithPassMiddleware->stackUp(new PassMiddleware());

        return [
            [$appWithEmptyStack],
            [$appWithPassMiddleware],
        ];
    }

    /**
     * @test
     * @dataProvider applicationsThatReturnResponse
     */
    public function processServerRequestDelegatingItToTheMiddlewareStack(
        Application $application,
        ResponseInterface $expectedResponse
    ) {
        $serverRequest = $this->createMock(ServerRequestInterface::class);

        $response = $application->process($serverRequest);

        $this->assertSame($expectedResponse, $response);
    }

    public function applicationsThatReturnResponse(): array
    {
        $aResponse = $this->createMock(ResponseInterface::class);
        $anotherResponse = $this->createMock(ResponseInterface::class);

        $passMiddleware = new PassMiddleware();
        $aFixedResponseMiddleware = new FixedResponseMiddleware($aResponse);
        $anotherFixedResponseMiddleware = new FixedResponseMiddleware($anotherResponse);

        $anApplication = new Application();
        $anApplication->stackUp($aFixedResponseMiddleware);
        $anApplication->stackUp($passMiddleware);

        $anotherApplication = new Application();
        $anotherApplication->stackUp($aFixedResponseMiddleware);
        $anotherApplication->stackUp($anotherFixedResponseMiddleware);
        $anotherApplication->stackUp($passMiddleware);

        return [
            [$anApplication, $aResponse],
            [$anotherApplication, $anotherResponse],
        ];
    }
}
