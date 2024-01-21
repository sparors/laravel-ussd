<?php

namespace Sparors\Ussd\Contracts;

use Sparors\Ussd\Ussd;

interface Configurator
{
    public function configure(Ussd $ussd): void;
}
