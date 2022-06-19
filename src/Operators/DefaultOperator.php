<?php

namespace Sparors\Ussd\Operators;

use Sparors\Ussd\Contracts\OperatorContract;
use Sparors\Ussd\Machine;

class DefaultOperator implements OperatorContract
{
    public function decorate(Machine $machine): Machine
    {
        return $machine;
    }
}
