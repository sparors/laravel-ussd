<?php

namespace Sparors\Ussd\Contracts;

use Sparors\Ussd\Machine;

interface UssdOperatorContract
{
    public function decorate(Machine $machine): Machine;
}