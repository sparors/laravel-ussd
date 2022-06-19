<?php

namespace Sparors\Ussd\Contracts;

use Sparors\Ussd\Machine;

interface OperatorContract
{
    public function decorate(Machine $machine): Machine;
}
