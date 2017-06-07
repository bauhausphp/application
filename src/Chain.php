<?php

namespace Bauhaus\MiddlewareChain;

use InvalidArgumentException;
use Interop\Http\ServerMiddleware\MiddlewareInterface as Middleware;
use Interop\Http\ServerMiddleware\DelegateInterface as Delegate;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

class Chain
{
    private $diContainer;
    private $middlewareStack = [];

    private function __construct(?Container $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    public function stackUp($middleware): void
    {
        if (false === $this->canStackUp($middleware)) {
            throw new InvalidArgumentException(
                'Can only stack up PSR-15 middlewares'
            );
        }

        $this->middlewareStack[] = $middleware;
    }

    public function handle(ServerRequest $request): Response
    {
        $firstDelegator = $this->buildChain();

        return $firstDelegator->process($request);
    }

    private function canStackUp($middleware): bool
    {
        if (is_object($middleware)) {
            return $middleware instanceof Middleware;
        }

        if (false === is_string($middleware)) {
            return false;
        }

        if (false === class_exists($middleware)) {
            return false;
        }

        $implementedInterfaces = class_implements($middleware);

        return in_array(Middleware::class, $implementedInterfaces);
    }

    private function buildChain(): Delegate
    {
        $firstDelegator = new GroundDelegator();

        foreach ($this->middlewareStack as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->diContainer->get($middleware);
            }

            $firstDelegator = new Delegator($middleware, $firstDelegator);
        }

        return $firstDelegator;
    }

    public static function create()
    {
        $withoutDiContainer = null;

        return new self($withoutDiContainer);
    }

    public static function createWithDiContainer(Container $diContainer)
    {
        return new self($diContainer);
    }
}
