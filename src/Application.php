<?php

namespace Bauhaus;

use InvalidArgumentException;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Bauhaus\Application\Delegator;
use Bauhaus\Application\GroundDelegator;
use Bauhaus\Application\GroundDelegatorReachedException;

class Application
{
    private $middlewareStack = [];

    public function stackUp($middleware): void
    {
        if (false === $this->canStackUp($middleware)) {
            throw new InvalidArgumentException(
                'Can only stack up PSR-15 middlewares'
            );
        }

        $this->middlewareStack[] = $middleware;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $firstHandler = $this->buildChain();

        return $firstHandler->process($request);
    }

    private function canStackUp($middleware): bool
    {
        if (is_object($middleware)) {
            return $middleware instanceof MiddlewareInterface;
        }

        if (false === is_string($middleware)) {
            return false;
        }

        if (false === class_exists($middleware)) {
            return false;
        }

        $implementedInterfaces = class_implements($middleware);

        return in_array(MiddlewareInterface::class, $implementedInterfaces);
    }

    private function buildChain(): DelegateInterface
    {
        $currentDelegator = new GroundDelegator();

        foreach ($this->middlewareStack as $middleware) {
            $currentDelegator = new Delegator($middleware, $currentDelegator);
        }

        return $currentDelegator;
    }
}
