<?php

namespace Bauhaus\Middlewares;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class FixedResponseMiddleware implements MiddlewareInterface
{
    private $fixedResponse;

    public function __construct($fixedResponse)
    {
        $this->fixedResponse = $fixedResponse;
    }

    public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ) {
        return $this->fixedResponse;
    }
}
