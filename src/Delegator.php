<?php

namespace Bauhaus\MiddlewareChain;

use Interop\Http\ServerMiddleware\MiddlewareInterface as Middleware;
use Interop\Http\ServerMiddleware\DelegateInterface as Delegate;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class Delegator implements Delegate
{
    private $middleware;
    private $nextDelegator;

    public function __construct(
        Middleware $middleware,
        Delegate $nextDelegator
    ) {
        $this->middleware = $middleware;
        $this->nextDelegator = $nextDelegator;
    }

    public function process(ServerRequest $request)
    {
        return $this->middleware->process($request, $this->nextDelegator);
    }
}
