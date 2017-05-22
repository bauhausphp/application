<?php

namespace Bauhaus;

use InvalidArgumentException;
use Interop\Http\ServerMiddleware\MiddlewareInterface as Psr15Middleware;

class Application
{
    private $stack = [];

    public function stack(): array
    {
        return $this->stack;
    }

    public function stackUp($middleware): void
    {
        if (!$middleware instanceof Psr15Middleware) {
            throw new InvalidArgumentException(
                'Only PSR-15 Middlewares can be stacked up'
            );
        }

        $this->stack[] = $middleware;
    }
}
