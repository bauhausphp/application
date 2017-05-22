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
}
