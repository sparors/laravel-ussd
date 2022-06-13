<?php

namespace Sparors\Ussd\Tests\Operators;

use Sparors\Ussd\Contracts\OperatorContract;
use Sparors\Ussd\Machine;

class TestOperator implements OperatorContract
{
    public $response;

    public function __construct()
    {
        $this->response = function (string $message, string $action) {
            return ['action' => $action, 'operator' => 'Test', 'message' => $message];
        };
    }

    public function decorate(Machine $machine): Machine
    {
        return $machine->setFromRequest([
            'phone_number' => 'phoneNumber',
            'network' => 'serviceCode',
            'session_id' => 'sessionId'
        ])
            ->setResponse($this->response);
    }
}
