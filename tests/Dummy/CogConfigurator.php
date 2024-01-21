<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Ussd;

class CogConfigurator implements Configurator
{
    public function __construct(
        private string $operator = 'Default'
    ) { }

    public function configure(Ussd $ussd): void
    {
        $ussd->useResponse(function (string $message, string $terminating) {
            return ['action' => $terminating ? 'prompt' : 'input', 'operator' => $this->operator, 'message' => $message];
        });
    }
}
