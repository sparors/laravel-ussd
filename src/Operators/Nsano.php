<?php

namespace Sparors\Ussd\Operators;

use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Machine;

class Nsano implements Configurator
{
    public function configure(Machine $machine): void
    {
        $machine->set([
            'phone_number' => request('msisdn'),
            'network' => request('network'),
            'session_id' => request('UserSessionID'),
            'input' => request('msg')
        ])
            ->setResponse(function (string $message, string $action) {
                return [
                    'USSDResp' => [
                        'action' => $action === '2' ? 'prompt' : 'input',
                        'menus' => '',
                        'title' => $message
                    ]
                ];
            });
    }
}
