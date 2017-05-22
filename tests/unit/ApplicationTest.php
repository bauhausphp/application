<?php

namespace Bauhaus;

use PHPUnit\Framework\TestCase;

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

        $this->application->stackUp($middleware);

        $this->assertEquals([$middleware], $this->application->stack());
    }

    /**
     * @test
     */
    public function stackUpPsr15MiddlewareFromString()
    {
        $this->application->stackUp(PassMiddleware::class);

        $this->assertEquals([PassMiddleware::class], $this->application->stack());
    }

    /**
     * @test
     * @dataProvider notPsr15Middlewares
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only PSR-15 Middlewares can be stacked up
     */
    public function exceptionOccursWhenTryingToStackUpANotPsr15Middleware(
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
}
