<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Machine;

class CogConfigurator implements Configurator
{
    public $response;

    public function __construct(string $operator = 'Default')
    {
        $this->response = function (string $message, string $action) use ($operator) {
            return ['action' => $action, 'operator' => $operator, 'message' => $message];
        };
    }

    public function configure(Machine $machine): void
    {
        $machine->setFromRequest([
            'phone_number' => 'phoneNumber',
            'network' => 'serviceCode',
            'session_id' => 'sessionId'
        ])->setResponse($this->response);
    }
}
