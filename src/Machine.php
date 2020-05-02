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
                'message' => $message,
                'code' => $code,
            ];
        };
    }

    public function run()
    {
        throw_if(is_null($this->sessionId), Exception::class, 'SessionId needs to be set before ussd machine can run.');
        
        $this->record = new Record(Cache::store($this->store), $this->sessionId);

        $this->saveParameters();

        if ($this->record->has('__init')) {
            $active = $this->record->get('__active');

            throw_if(! class_exists($active), Exception::class, 'Active State Class needs to be set before ussd machine can run. It may be that your session has ended.');

            $activeClass = new $active;

            $state = $activeClass->next($this->input);
            
            throw_if(! class_exists($state), Exception::class, 'Continuing State Class needs to be set before ussd machine can run. It may be that your session has ended.');
            
            $stateClass = new $state;
            
            $stateClass->setRecord($this->record);
            $this->record->set('__active', $state);
        } else {
            throw_if(! class_exists($this->initialState), Exception::class, 'Initial State Class needs to be set before ussd machine can run.');

            $this->record->set('__active', $this->initialState);
            $this->record->set('__init', true);


            $stateClass = new $this->initialState;
            $stateClass->setRecord($this->record);
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
        $this->saveParameter('sessionId', $this->sessionId);
        $this->saveParameter('phoneNumber', $this->phoneNumber);
        $this->saveParameter('network', $this->network);
        $this->saveParameter('input', $this->input);
    }
}