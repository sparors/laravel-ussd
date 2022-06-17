<?php

namespace Sparors\Ussd\Tests\Operators;

use Sparors\Ussd\Machine;

class NotInstanceOfOperator
{
    public function decorate(Machine $machine): Machine
    {
        return $machine;
    }
}
