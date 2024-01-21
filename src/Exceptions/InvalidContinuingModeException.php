<?php

namespace Sparors\Ussd\Exceptions;

use Sparors\Ussd\ContinuingMode;

class InvalidContinuingModeException extends UssdException
{
    public function __construct(int $continuingMode)
    {
        $start = ContinuingMode::START;
        $continue = ContinuingMode::CONTINUE;
        $confirm = ContinuingMode::CONFIRM;

        parent::__construct("Invalid continuingMode, {$continuingMode} should be one of {$start}, {$continue} or {$confirm}. use constants from ".ContinuingMode::class);
    }
}
