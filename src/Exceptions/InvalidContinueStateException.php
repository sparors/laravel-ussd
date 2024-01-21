<?php

namespace Sparors\Ussd\Exceptions;

use Sparors\Ussd\Contracts\ContinueState;

class InvalidContinueStateException extends UssdException
{
    public function __construct(?string $state)
    {
        $message = $state
            ? "Invalid continue state, {$state} should implement ". ContinueState::class
            : "Invalid continue state, should not be null and must implement ". ContinueState::class;

        parent::__construct($message);
    }
}
