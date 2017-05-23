<?php

namespace Bauhaus;

use InvalidArgumentException;
use Interop\Http\ServerMiddleware\MiddlewareInterface as Middleware;

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

    private function canBeStackedUp($middleware): bool
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
}
