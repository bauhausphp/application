<?php

namespace Bauhaus\MiddlewareStack;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class TestCase extends PHPUnitTestCase
{
    protected function newDummyResponse(): Response
    {
        return $this->createStub(Response::class);
    }

    protected function newDummyServerRequest(): ServerRequest
    {
        return $this->createStub(ServerRequest::class);
    }

    protected function newDummyRequestHandler(): RequestHandler
    {
        return $this->createStub(RequestHandler::class);
    }
}
