<?php

namespace Bauhaus\MiddlewareStack;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MiddlewareStack implements RequestHandler
{
    private array $middlewares;

    public function __construct(Middleware ...$middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public static function lazy(Container $container, string ...$middlewareIds): self
    {
        $lazyMiddlewares = array_map(fn(string $id) => new LazyMiddleware($container, $id), $middlewareIds);

        return new self(...$lazyMiddlewares);
    }

    public function handle(ServerRequest $request): Response
    {
        return $this->buildChain()->handle($request);
    }

    private function buildChain(): RequestHandler
    {
        $middlewares = array_reverse($this->middlewares);
        $lastHandler = new GroundDelegator();

        return array_reduce(
            $middlewares,
            fn(RequestHandler $next, Middleware $middleware) => new Delegator($middleware, $next),
            $lastHandler,
        );
    }
}
