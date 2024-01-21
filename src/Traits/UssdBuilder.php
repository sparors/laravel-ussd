<?php

namespace Sparors\Ussd\Traits;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\App;
use Sparors\Ussd\Context;
use Sparors\Ussd\ContinuingMode;
use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Contracts\ExceptionHandler;
use Sparors\Ussd\Contracts\InitialAction;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Contracts\Response;
use Sparors\Ussd\Exceptions\InvalidConfiguratorException;
use Sparors\Ussd\Exceptions\InvalidContinueStateException;
use Sparors\Ussd\Exceptions\InvalidContinuingModeException;
use Sparors\Ussd\Exceptions\InvalidExceptionHandlerException;
use Sparors\Ussd\Exceptions\InvalidInitialStateException;
use Sparors\Ussd\Exceptions\InvalidResponseException;

trait UssdBuilder
{
    public function useContext(Context $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function useConfigurator(Configurator|string $configurator): static
    {
        if (is_string($configurator) && class_exists($configurator)) {
            $configurator = App::make($configurator);
        }

        throw_unless(
            $configurator instanceof Configurator,
            InvalidConfiguratorException::class,
            $configurator::class
        );

        $configurator->configure($this);

        return $this;
    }

    public function useInitialState(InitialAction|InitialState|string $initialState): static
    {
        if (is_string($initialState) && class_exists($initialState)) {
            $initialState = App::make($initialState);
        }

        throw_unless(
            $initialState instanceof InitialState || $initialState instanceof InitialAction,
            InvalidInitialStateException::class,
            $initialState::class
        );

        $this->initialState = $initialState;

        return $this;
    }

    public function useContinuingState(
        int $continuingMode,
        null|DateInterval|DateTimeInterface|int $continuingTtl,
        null|ContinueState|string $continuingState = null
    ): static {
        if (is_string($continuingState) && class_exists($continuingState)) {
            $continuingState = App::make($continuingState);
        }

        throw_unless(
            in_array($continuingMode, [ContinuingMode::START, ContinuingMode::CONTINUE, ContinuingMode::CONFIRM], true),
            InvalidContinuingModeException::class,
            $continuingMode
        );

        $this->continuingMode = $continuingMode;
        $this->continuingTtl = $continuingTtl;

        if (ContinuingMode::CONFIRM === $continuingMode) {
            throw_unless(
                $continuingState instanceof ContinueState,
                InvalidContinueStateException::class,
                isset($continuingState) ? $continuingState::class : null
            );
        }

        $this->continuingState = $continuingState;

        return $this;
    }

    public function useResponse(Closure|Response|string $response): static
    {
        if (is_string($response) && class_exists($response)) {
            $response = App::make($response);
        }

        throw_unless(
            $response instanceof Response || $response instanceof Closure,
            InvalidResponseException::class,
            $response::class
        );

        $this->response = $response;

        return $this;
    }

    public function useExceptionHandler(Closure|ExceptionHandler|string $exceptionHandler): static
    {
        if (is_string($exceptionHandler) && class_exists($exceptionHandler)) {
            $exceptionHandler = App::make($exceptionHandler);
        }

        throw_unless(
            $exceptionHandler instanceof ExceptionHandler || $exceptionHandler instanceof Closure,
            InvalidExceptionHandlerException::class,
            $exceptionHandler::class
        );

        $this->exceptionHandler = $exceptionHandler;

        return $this;
    }

    public function useStore(string $storeName): static
    {
        $this->storeName = $storeName;

        return $this;
    }
}
