<?php

namespace Bauhaus;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MiddlewareThatDelegates implements Middleware
{
    public function process(ServerRequest $request, RequestHandler $handler): Response
    {
        return $handler->handle($request);
    }
}
