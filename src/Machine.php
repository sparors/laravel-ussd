<?php

namespace Sparors\Ussd;

use Exception;
use Illuminate\Support\Facades\Cache;

class Machine
{
    use HasManipulators;

    protected $initialState;

    protected $store;
    /** @var Record */
    protected $record;
    
    protected $sessionId;
    protected $phoneNumber;
    protected $network;
    protected $input;
    protected $response;

    public function __construct()
    {
        $this->sessionId = null;
        $this->phoneNumber = null;
        $this->network = null;
        $this->input = null;
        $this->response = function (string $message, int $code) {
            return [
                'default_message' => $message,
                'default_code' => $code,
            ];
        };
    }

    public function run()
    {
        throw_if(is_null($this->sessionId), Exception::class, 'SessionId needs to be set be ussd machine can run.');
        
        $this->record = new Record(Cache::store($this->store), $this->sessionId);

        $this->saveParameters();

        if ($this->record->has('__init')) {
            $active = $this->record->get('__active');

            $activeClass = new $active;

            $state = $activeClass->next($this->input);
            $stateClass = new $state;
            $this->record->set('__active', $state);
        } else {
            $this->record->set('__active', $this->initialState);
            $this->record->set('__init', true);

            $stateClass = new $this->initialState;
        }
        return ($this->response)($stateClass->render(), $stateClass->getType());
    }

    private function saveParameter(string $key, $value)
    {
        if (! is_null($value)) {
            $this->record->set($key, $value);
        }
    }

    private function saveParameters()
    {
        $this->saveParameter('__sessionId', $this->sessionId);
        $this->saveParameter('__phoneNumber', $this->phoneNumber);
        $this->saveParameter('__network', $this->network);
        $this->saveParameter('__input', $this->input);
    }
}