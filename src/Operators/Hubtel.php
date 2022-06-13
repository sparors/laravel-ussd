<?php

namespace Sparors\Ussd\Operators;

use Sparors\Ussd\Contracts\OperatorContract;
use Sparors\Ussd\Machine;

class Hubtel implements OperatorContract
{
    public function decorate(Machine $machine): Machine
    {
        return $machine->set([
            'phone_number' => request('Mobile'),
            'network' => request('Operator'),
            'session_id' => request('SessionId'),
            'input' => request('Message')
        ])
            ->setResponse(function(string $message, string $action) {
                return [
                    'Mobile' => request('Mobile'),
                    'SessionId' => request('SessionId'),
                    'Message' => $message,
                    'Type' => $action === 'prompt' ? 'Release' : 'Response'
                ];
            });
    }
}