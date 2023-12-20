<?php

namespace Sparors\Ussd\Tests;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Str;
use Sparors\Ussd\ContinuingMode;
use Sparors\Ussd\Contracts\Response;
use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Contracts\ExceptionHandler;
use Sparors\Ussd\Contracts\InitialAction;

class PendingTest
{
    private array $uses;
    private string $actor;
    private ?Closure $response;
    private array $additional;
    private ?string $storeName;
    private ?Closure $exceptionHandler;

    public function __construct(
        private string|InitialState|InitialAction $initialState,
        private int $continuingMode = ContinuingMode::START,
        private null|int|DateInterval|DateTimeInterface $continuingTtl = null,
        private null|string|ContinueState $continuingState = null
    ) {
        $this->uses = [];
        $this->additional = [];
        $this->response = null;
        $this->storeName = null;
        $this->exceptionHandler = null;
        $this->actor = Str::random(8);
    }

    public function additional(array $additional): static
    {
        $this->additional = $additional;

        return $this;
    }

    public function use(string|Configurator|Response|ExceptionHandler $use)
    {
        $this->uses[] = $use;

        return $this;
    }

    public function useResponse(Closure $response)
    {
        $this->response = $response;

        return $this;
    }

    public function useExceptionHandler(Closure $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;

        return $this;
    }

    public function useStore(string $storeName)
    {
        $this->storeName = $storeName;

        return $this;
    }

    public function actingAs(string $key)
    {
        $this->actor = $key;

        return $this;
    }

    public function start()
    {
        return new Testing(
            $this->initialState,
            $this->continuingMode,
            $this->continuingTtl,
            $this->continuingState,
            $this->storeName,
            $this->additional,
            $this->uses,
            $this->response,
            $this->exceptionHandler,
            $this->actor
        );
    }
}
