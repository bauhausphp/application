<?php

namespace Bauhaus\Application;

use RuntimeException;

class GroundDelegatorReachedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Ground delegator reached');
    }
}
