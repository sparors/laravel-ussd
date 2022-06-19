<?php

namespace Sparors\Ussd\Operators;

use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Machine;

class DefaultOperator implements Configurator
{
    public function configure(Machine $machine): void
    {
        $machine;
    }
}
