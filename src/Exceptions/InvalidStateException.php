<?php

namespace Sparors\Ussd\Exceptions;

use Sparors\Ussd\Contracts\State;

class InvalidStateException extends UssdException
{
    public function __construct(string $state)
    {
        parent::__construct("Invalid state, {$state} should implement ".State::class);
    }
}
