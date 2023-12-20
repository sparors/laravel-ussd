<?php

namespace Sparors\Ussd\Exceptions;

use Exception;
use Sparors\Ussd\Contracts\State;

class NextStateNotFoundException extends Exception
{
    public function __construct(State $state)
    {
        $class = $state::class;

        parent::__construct("No state found after {$class} with the given input");
    }
}
