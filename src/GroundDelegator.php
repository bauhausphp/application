<?php

namespace Bauhaus\MiddlewareChain;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;

class GroundDelegator implements DelegateInterface
{
    public function process(ServerRequestInterface $request)
    {
        throw new GroundDelegatorReachedException($request);
    }
}
