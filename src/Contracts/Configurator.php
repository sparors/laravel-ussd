<?php

namespace Sparors\Ussd\Contracts;

use Sparors\Ussd\Machine;

interface Configurator
{
    public function configure(Machine $machine): void;
}
