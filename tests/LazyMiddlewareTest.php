<?php

namespace Bauhaus\MiddlewareStack;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Server\MiddlewareInterface as Middleware;

class LazyMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function processByCallingMiddlewareLoadedFromContainer(): void
    {
        $dummyRequest = $this->newDummyServerRequest();
        $dummyRequestHandler = $this->newDummyRequestHandler();
        $loadedMiddleware = $this->createMock(Middleware::class);
        $container = $this->createMock(Container::class);

        $container
            ->method('get')
            ->with('middleware-id')
            ->willReturn($loadedMiddleware);
        $loadedMiddleware
            ->expects($this->once())
            ->method('process')
            ->with($dummyRequest, $dummyRequestHandler);

        $lazyMiddleware = new LazyMiddleware($container, 'middleware-id');
        $lazyMiddleware->process($dummyRequest, $dummyRequestHandler);
    }

    /**
     * @test
     */
    public function throwExceptionIfContainerDoesNotReturnAMiddleware(): void
    {
        $dummyRequest = $this->newDummyServerRequest();
        $dummyRequestHandler = $this->newDummyRequestHandler();
        $container = $this->createMock(Container::class);

        $container
            ->method('get')
            ->with('middleware-id')
            ->willReturn('anything');
        $this->expectException(LazyMiddlewareContainerReturnedNotAMiddleware::class);

        $lazyMiddleware = new LazyMiddleware($container, 'middleware-id');
        $lazyMiddleware->process($dummyRequest, $dummyRequestHandler);
    }
}
