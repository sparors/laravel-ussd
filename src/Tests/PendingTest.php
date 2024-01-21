<?php

namespace Sparors\Ussd\Tests;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Str;
use Sparors\Ussd\ContinuingMode;
use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Contracts\ExceptionHandler;
use Sparors\Ussd\Contracts\InitialAction;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Contracts\Response;

class PendingTest
{
    private array $uses;
    private string $actor;
    private array $additional;
    private ?string $storeName;

    public function __construct(
        private InitialAction|InitialState|string $initialState,
        private int $continuingMode = ContinuingMode::START,
        private null|DateInterval|DateTimeInterface|int $continuingTtl = null,
        private null|ContinueState|string $continuingState = null
    ) {
        $this->uses = [];
        $this->additional = [];
        $this->storeName = null;
        $this->actor = Str::random(8);
    }

    public function additional(array $additional): static
    {
        $this->additional = $additional;

        return $this;
    }

    public function use(Closure|Configurator|ExceptionHandler|Response|string $use): static
    {
        $this->uses[] = $use;

        return $this;
    }

    public function useStore(string $storeName): static
    {
        $this->storeName = $storeName;

        return $this;
    }

    public function actingAs(string $key): static
    {
        $this->actor = $key;

        return $this;
    }

    public function start(string $input = ''): Testing
    {
        return new Testing(
            $this->initialState,
            $this->continuingMode,
            $this->continuingTtl,
            $this->continuingState,
            $this->storeName,
            $this->additional,
            $this->uses,
            $this->actor,
            $input
        );
    }
}
