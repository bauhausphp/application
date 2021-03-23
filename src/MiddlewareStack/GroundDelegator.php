<?php

namespace Bauhaus\MiddlewareStack;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * @internal
 */
class GroundDelegator implements RequestHandler
{
    public function handle(ServerRequest $request): Response
    {
        throw new GroundDelegatorReached();
    }
}
