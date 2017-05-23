<?php

namespace Bauhaus;

use InvalidArgumentException;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Application
{
    private $stack = [];

    public function stack(): array
    {
        return $this->stack;
    }

    public function stackUp($middleware): void
    {
        if (false === $this->canBeStackedUp($middleware)) {
            throw new InvalidArgumentException(
                'Only PSR-15 Middlewares can be stacked up'
            );
        }

        $this->stack[] = $middleware;
    }

    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $headDelegator = $this->buildDelegatorChain();

        return $headDelegator->process($request);
    }

    private function canBeStackedUp($middleware): bool
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

    private function buildDelegatorChain(): DelegateInterface
    {
        $currentDelegator = new GroundDelegator();

        foreach($this->stack as $middleware) {
            $currentDelegator = new Delegator($middleware, $currentDelegator);
        }

        return $currentDelegator;
    }
}
