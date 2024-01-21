<?php

namespace Sparors\Ussd\Exceptions;

use Sparors\Ussd\Contracts\InitialAction;
use Sparors\Ussd\Contracts\InitialState;

class InvalidInitialStateException extends UssdException
{
    public function __construct(string $state)
    {
        parent::__construct("Invalid initial state, {$state} should implement ".InitialState::class." or ".InitialAction::class);
    }
}
