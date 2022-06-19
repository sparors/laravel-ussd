<?php

namespace Sparors\Ussd\Operators;

use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Machine;

class Hubtel implements Configurator
{
    public function configure(Machine $machine): void
    {
        $machine->set([
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
