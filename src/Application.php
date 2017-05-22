<?php

namespace Bauhaus;

use InvalidArgumentException;

class Application
{
    private $stack = [];

    public function stack(): array
    {
        return $this->stack;
    }

    public function stackUp($middleware): void
    {
        $this->stack[] = $middleware;
    }
}
