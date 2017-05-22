<?php

namespace Bauhaus;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * @test
     */
    public function stackUpPsr15Middleware()
    {
        $application = new Application();
        $middleware = new PassMiddleware();

        $application->stackUp($middleware);

        $this->assertEquals([$middleware], $application->stack());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Only PSR-15 Middlewares can be stacked up
     */
    public function exceptionOccursWhenTryingToStackUpANotPsr15Middleware()
    {
        $application = new Application();
        $notPsr15Middleware = new NotPsr15Middleware();

        $application->stackUp($notPsr15Middleware);
    }

    //public function notPsr15Middlewares()
    //{
    //    return [
    //        [NotPsr15Middleware::class],
    //        [new NotPsr15Middleware()],
    //    ];
    //}
}
