<?php

namespace Bauhaus\MiddlewareChain;

use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

class GroundDelegatorReachedException extends RuntimeException
{
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;

        parent::__construct('Ground delegator reached');
    }
}
