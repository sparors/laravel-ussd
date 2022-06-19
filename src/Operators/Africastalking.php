<?php

namespace Sparors\Ussd\Operators;

use Sparors\Ussd\Contracts\OperatorContract;
use Sparors\Ussd\Machine;
use Sparors\Ussd\State;

class Africastalking implements OperatorContract
{
    public function decorate(Machine $machine): Machine
    {
        return $machine->setFromRequest([
            'phone_number',
            'network' => 'serviceCode',
            'session_id' => 'sessionId'
        ])
            ->setInput(
                strpos(request('text'), '*') !== false ?
                    substr(request('text'), strrpos(request('text'), '*') + 1) :
                    request('text')
            )
            ->setResponse(function (string $message, string $action) {
                return $action === State::PROMPT ? "END $message" : "CON $message";
            });
    }
}
