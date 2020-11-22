<?php

namespace Bauhaus\MiddlewareStack;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class LazyMiddleware implements Middleware
{
    public function __construct(private Container $container, private string $id) {}

    public function process(ServerRequest $request, RequestHandler $handler): Response
    {
        $middleware = $this->container->get($this->id);

        return match (true) {
            $middleware instanceof Middleware => $middleware->process($request, $handler),
            default => throw new LazyMiddlewareContainerReturnedNotAMiddleware(),
        };
    }
}
