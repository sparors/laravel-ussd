<?php

namespace Sparors\Ussd\Exceptions;

class NextStateNotFoundException extends UssdException
{
    public function __construct(string $state)
    {
        parent::__construct("Next state not found after {$state}. This may indicate unhandled transition");
    }
}
