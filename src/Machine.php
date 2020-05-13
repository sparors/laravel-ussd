<?php

namespace Sparors\Ussd;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;

class Machine
{
    use HasManipulators;

    /** @var string|callable|Closure */
    protected $initialState;

    /** @var string|null */
    protected $store;

    /** @var Record */
    protected $record;
    
    /** @var string|null */
    protected $sessionId;

    /** @var string|null */
    protected $phoneNumber;

    /** @var string|null */
    protected $network;

    /** @var string|null */
    protected $input;

    /** @var string|null */
    protected $response;

    public function __construct()
    {
        $this->sessionId = null;
        $this->phoneNumber = null;
        $this->network = null;
        $this->input = null;
        $this->store = config('ussd.cache_store', null);
        $this->response = function (string $message, string $action) {
            return [
                'message' => $message,
                'action' => $action,
            ];
        };
    }

    public function run(): array
    {
        $this->ensureSessionIdIsSet($this->sessionId);
        
        $this->record = new Record(
            Cache::store($this->store),
            $this->sessionId
        );

        $this->saveParameters();

        if ($this->record->has('__init')) {
            $active = $this->record->get('__active');

            $this->ensureClassExist(
                $active, 
                'Active State Class needs to be set before ussd machine can'
                . ' run. It may be that your session has ended.'
            );

            $activeClass = new $active;
            $activeClass->setRecord($this->record);

            $state = $activeClass->next($this->input);
            
            $this->ensureClassExist(
                $state,
                'Continuing State Class needs to be set before ussd '
                . 'machine can run. It may be that your session has ended.'
            );
            
            $stateClass = new $state;
            $stateClass->setRecord($this->record);
            
            $this->record->set('__active', $state);
        } else {
            
            $this->processInitialState();

            $this->ensureClassExist(
                $this->initialState,
                'Initial State Class needs to be set before'
                . ' ussd machine can run.'
            );

            $this->record->set('__active', $this->initialState);
            $this->record->set('__init', true);


            $stateClass = new $this->initialState;
            $stateClass->setRecord($this->record);
        }

        return ($this->response)($stateClass->render(), $stateClass->getAction());
    }

    private function saveParameter(string $key, $value)
    {
        if (!is_null($value)) {
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

    /**
     * @throws Exception
     */
    private function ensureClassExist(string $class, string $message): void
    {
        throw_if(
            !class_exists($class),
            Exception::class,
            $message
        );
    }

    /**
     * @throws Exception
     */
    private function ensureSessionIdIsSet(?string $session): void
    {
        throw_if(
            is_null($session),
            Exception::class,
            'SessionId needs to be set before ussd machine can run.'
        );
    }


    private function processInitialState(): void
    {
        if (is_callable($this->initialState)) {
            $this->initialState = ($this->initialState)();
        }
    }
}
