<?php

namespace Sparors\Ussd;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;
use Sparors\Ussd\Contracts\Configurator;

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

    public function run()
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
                'Active State Class needs to be set before ussd machine can '
                . 'run. It may be that your session has ended.'
            );

            $activeClass = new $active();
            $activeClass->setRecord($this->record);

            $state = $activeClass->next($this->input);

            $this->processAction(
                $stateClass,
                $state,
                'Continuing State Class needs to be set before ussd '
                . 'machine can run. It may be that your session has ended.'
            );

            $this->record->set('__active', $state);
        } else {
            $this->processInitialState();

            $state = $this->initialState;

            $this->processAction(
                $stateClass,
                $state,
                'Initial State Class needs to be set before '
                . 'ussd machine can run.'
            );

            $this->record->set('__active', $state);
            $this->record->set('__init', true);
        }

        return ($this->response)($stateClass->render(), $stateClass->getAction());
    }

    /** @param Configurator|string $configurator */
    public function useConfigurator($configurator): self
    {
        if (is_string($configurator) && class_exists($configurator)) {
            $configurator = new $configurator();
        }

        throw_if(
            !$configurator instanceof Configurator,
            Exception::class,
            "configurator does not implement Sparors\Ussd\Contracts\Configurator interface."
        );

        $configurator->configure($this);

        return $this;
    }

    protected function saveParameter(string $key, $value)
    {
        if (!is_null($value)) {
            $this->record->set($key, $value);
        }
    }

    protected function saveParameters()
    {
        $this->saveParameter('sessionId', $this->sessionId);
        $this->saveParameter('phoneNumber', $this->phoneNumber);
        $this->saveParameter('network', $this->network);
        $this->saveParameter('input', $this->input);
    }

    /**
     * @throws Exception
     */
    protected function ensureClassExist(?string $class, string $message): void
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
    protected function ensureSessionIdIsSet(?string $session): void
    {
        throw_if(
            is_null($session),
            Exception::class,
            'SessionId needs to be set before ussd machine can run.'
        );
    }


    protected function processInitialState(): void
    {
        if (is_callable($this->initialState)) {
            $this->initialState = ($this->initialState)();
        }
    }

    protected function processAction(&$stateClass, &$state, $exception): void
    {
        $this->ensureClassExist(
            $state,
            $exception
        );

        $stateClass = new $state();
        $stateClass->setRecord($this->record);

        if (is_subclass_of($stateClass, Action::class)) {
            $state = $stateClass->run();

            $this->ensureClassExist(
                $state,
                'Ussd Action Class needs to return next State Class'
            );

            $stateClass = new $state();
            $stateClass->setRecord($this->record);
        }
    }
}
