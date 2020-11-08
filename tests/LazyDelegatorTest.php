<?php

namespace Bauhaus\MiddlewareChain;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class LazyDelegatorTest extends TestCase
{
    /**
     * @test
     */
    public function delegateRequestToMiddlewareLoadedFromContainer(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $container = $this->createMock(ContainerInterface::class);
        $delegatedMiddleware = $this->createMock(MiddlewareInterface::class);

        $delegatedMiddleware
            ->expects($this->once())
            ->method('process')
            ->with($request);
        $container
            ->expects($this->once())
            ->method('get')
            ->with('container-id')
            ->willReturn();

        $lazyDelegator = new LazyDelegator($container, 'container-id');
        $lazyDelegator->process($request);
    }
}
