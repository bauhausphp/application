<?php

namespace Bauhaus\MiddlewareStack;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * @internal
 */
class Delegator implements RequestHandler
{
    public function __construct(
        private Middleware $middleware,
        private RequestHandler $nextHandler,
    ) {}

    public function handle(ServerRequest $request): Response
    {
        return $this->middleware->process($request, $this->nextHandler);
    }
}
