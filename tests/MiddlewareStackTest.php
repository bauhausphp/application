<?php

namespace Bauhaus\MiddlewareStack;

use Psr\Container\ContainerInterface as Container;

class MiddlewareStackTest extends TestCase
{
    /**
     * @test
     */
    public function throwExceptionIfNoMiddlewareReturnsResponse(): void
    {
        $stack = new MiddlewareStack(
            new MiddlewareThatDelegates(),
            new MiddlewareThatDelegates(),
            new MiddlewareThatDelegates(),
        );

        $this->expectException(GroundDelegatorReached::class);

        $stack->handle($this->newDummyServerRequest());
    }

    /**
     * @test
     */
    public function processServerRequestByCallingMiddlewaresInOrder(): void
    {
        $expectedResponse = $this->newDummyResponse();
        $notExpectedResponse = $this->newDummyResponse();
        $stack = new MiddlewareStack(
            new MiddlewareThatDelegates(),
            new MiddlewareThatDelegates(),
            new MiddlewareThatDelegates(),
            new MiddlewareWithFixedResponse($expectedResponse),
            new MiddlewareWithFixedResponse($notExpectedResponse),
            new MiddlewareWithFixedResponse($notExpectedResponse),
        );

        $response = $stack->handle($this->newDummyServerRequest());

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @test
     */
    public function canBeCreatedWithLazyMiddlewares(): void
    {
        $container = $this->createStub(Container::class);
        $middlewareIds = ['id-1', 'id-2', 'id-3'];

        $stack = MiddlewareStack::lazy($container, ...$middlewareIds);

        $expected = new MiddlewareStack(
            new LazyMiddleware($container, 'id-1'),
            new LazyMiddleware($container, 'id-2'),
            new LazyMiddleware($container, 'id-3'),
        );
        $this->assertEquals($expected, $stack);
    }
}
