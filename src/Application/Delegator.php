<?php

namespace Bauhaus\Application;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;

class Delegator implements DelegateInterface
{
    private $middleware;
    private $nextDelegator;

    public function __construct(
        MiddlewareInterface $middleware,
        DelegateInterface $nextDelegator
    ) {
        $this->middleware = $middleware;
        $this->nextDelegator = $nextDelegator;
    }

    public function process(ServerRequestInterface $request)
    {
        return $this->middleware->process($request, $this->nextDelegator);
    }
}
