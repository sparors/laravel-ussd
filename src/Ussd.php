<?php

namespace Sparors\Ussd;

use Closure;
use DateInterval;
use DateTimeInterface;
use Exception;
use ReflectionClass;
use InvalidArgumentException;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Contracts\Action;
use Illuminate\Support\Facades\App;
use Sparors\Ussd\Contracts\Response;
use Sparors\Ussd\Attributes\Terminate;
use Sparors\Ussd\Attributes\Transition;
use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Contracts\ExceptionHandler;
use Sparors\Ussd\Contracts\InitialAction;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Exceptions\NextStateNotFoundException;
use Sparors\Ussd\Tests\PendingTest;

class Ussd
{
    private const INIT = '__init__';
    private const HEAL = '__heal__';
    private const SPUR = '__spur__';
    private const HALT = '__halt__';
    private const CURB = '__curb__';
    private const LIVE = '__live__';

    private Context $context;
    private ?string $storeName;
    private int $continuingMode;
    private Response|Closure $response;
    private ?ContinueState $continuingState;
    private InitialState|InitialAction $initialState;
    private null|int|DateInterval|DateTimeInterface $continuingTtl;
    private ExceptionHandler|Closure $exceptionHandler;

    public function __construct(?Context $context)
    {
        if ($context) {
            $this->context = $context;
        }

        $this->continuingMode = ContinuingMode::START;
        $this->continuingTtl = null;
        $this->continuingState = null;

        $this->storeName = null;
        $this->response = fn (string $message, bool $terminating) => [
            'message' => $message,
            'terminating' => $terminating,
        ];
        $this->exceptionHandler = fn (Exception $exception) => $exception->getMessage();
    }

    public static function build(?Context $context = null)
    {
        return new static($context);
    }

    public static function test(string|InitialState $initialState, int $continuingMode = ContinuingMode::START, null|int|DateInterval|DateTimeInterface $continuingTtl = null, null|string|ContinueState $continuingState = null)
    {
        return new PendingTest($initialState, $continuingMode, $continuingTtl, $continuingState);
    }

    public function useContext(Context $context)
    {
        $this->context = $context;
    }

    public function useConfigurator(Configurator|string $configurator): static
    {
        if (is_string($configurator) && class_exists($configurator)) {
            $configurator = App::make($configurator);
        }

        throw_unless(
            $configurator instanceof Configurator,
            InvalidArgumentException::class,
            "Configurator should implement ".Configurator::class
        );

        $configurator->configure($this);

        return $this;
    }

    public function useInitialState(string|InitialState|InitialAction $initialState)
    {
        if (is_string($initialState) && class_exists($initialState)) {
            $initialState = App::make($initialState);
        }

        throw_unless(
            $initialState instanceof InitialState || $initialState instanceof InitialAction,
            InvalidArgumentException::class,
            "Initial state should implement ".InitialState::class." or ".InitialAction::class
        );

        $this->initialState = $initialState;

        return $this;
    }

    public function useContinuingState(int $continuingMode, null|int|DateInterval|DateTimeInterface $continuingTtl, null|string|ContinueState $continuingState = null)
    {
        if (is_string($continuingState) && class_exists($continuingState)) {
            $continuingState = App::make($continuingState);
        }

        $this->continuingMode = $continuingMode;
        $this->continuingTtl = $continuingTtl;

        if (ContinuingMode::CONFIRM === $continuingMode) {
            throw_unless(
                $continuingState instanceof ContinueState,
                InvalidArgumentException::class,
                "Continuing state should implement ".ContinueState::class
            );
        }

        $this->continuingState = $continuingState;

        return $this;
    }

    public function useResponse(string|Response|Closure $response)
    {
        if (is_string($response) && class_exists($response)) {
            $response = App::make($response);
        }

        throw_unless(
            $response instanceof Response || $response instanceof Closure,
            InvalidArgumentException::class,
            "Response should implement ".Response::class." or be a closure"
        );

        $this->response = $response;

        return $this;
    }

    public function useExceptionHandler(string|ExceptionHandler|Closure $exceptionHandler)
    {
        if (is_string($exceptionHandler) && class_exists($exceptionHandler)) {
            $exceptionHandler = App::make($exceptionHandler);
        }

        throw_unless(
            $exceptionHandler instanceof ExceptionHandler || $exceptionHandler instanceof Closure,
            InvalidArgumentException::class,
            "Exception handler should implement ".ExceptionHandler::class." or be a closure"
        );

        $this->exceptionHandler = $exceptionHandler;

        return $this;
    }

    public function useStore(string $storeName)
    {
        $this->storeName = $storeName;

        return $this;
    }

    public function run(): mixed
    {
        try {
            [$message, $terminating] = $this->operate();
        } catch (Exception $exception) {
            [$message, $terminating] = $this->bail($exception);
        }

        return $this->response instanceof Response
            ? ($this->response)->respond($message, $terminating)
            : ($this->response)($message, $terminating);
    }

    private function operate(): array
    {
        App::instance($this->context::class, $this->context);

        $record = new Record($this->storeName, $this->context->uid(), $this->context->gid());

        if (ContinuingMode::CONTINUE === $this->continuingMode && !$record->has(static::CURB) && $spur = $record->get(static::SPUR, public: true)) {
            $record->setMany([static::HEAL => $spur, static::CURB => true]);
        } elseif (ContinuingMode::CONFIRM === $this->continuingMode && $record->has(static::HALT) && $spur = $record->get(static::SPUR, public: true)) {
            throw_unless(
                $this->continuingState instanceof ContinueState,
                $this->continuingState::class.' does not implement '.ContinueState::class
            );

            $record->forget(static::HALT);
            $record->set(static::CURB, true);

            if ($this->continuingState->confirm() === $this->context->input()) {
                $record->set(static::HEAL, $spur);
            }
        }

        if ($rid = $record->get(static::HEAL)) {
            $record = new Record($this->storeName, $rid, $this->context->gid());
        }

        App::instance($record::class, $record);

        if ($record->has(static::INIT)) {
            $nextState = $record->get(static::LIVE);

            throw_unless(
                $nextState,
                'No active state found. This may indicate session has ended'
            );

            $nextState = App::make($nextState);

            $nextState = $this->next($nextState);

            $nextState = $this->actionable($nextState);

            $record->set(static::LIVE, $nextState);
        } elseif (ContinuingMode::CONFIRM === $this->continuingMode && !$record->has(static::CURB) && $record->has(static::SPUR, true)) {
            $nextState = $this->continuingState::class;

            $record->set(static::HALT, true);
        } else {
            throw_unless(
                isset($this->initialState),
                'Initial state should be provided'
            );

            $nextState = $this->initialState::class;

            $nextState = $this->actionable($nextState);

            $record->setMany([
                static::LIVE => $nextState,
                static::INIT => true
            ]);

            if (ContinuingMode::START !== $this->continuingMode) {
                $record->set(static::SPUR, $this->context->uid(), $this->continuingTtl, true);
            }
        }

        $state = App::make($nextState);

        /** @var Menu */
        $menu = App::call([$state, 'render']);

        return [(string) $menu, $this->terminating($state)];
    }

    private function next(State $state): string
    {
        $reflected = new ReflectionClass($state);

        $attributes = $reflected->getAttributes(Transition::class);

        foreach($attributes as $attribute) {
            $transition = $attribute->newInstance();

            if (is_array($transition->decision)) {
                $transition->decision = new $transition->decision[0](...array_slice($transition->decision, 1));
            } elseif (is_string($transition->decision)) {
                $transition->decision = new $transition->decision;
            }

            if ($transition->decision->decide($this->context->input())) {
                if ($transition->callback) {
                    if (is_array($transition->callback) && is_string($transition->callback[0])) {
                        $transition->callback[0] = App::make($transition->callback[0]);
                    }

                    App::call($transition->callback);
                }

                return $transition->state;
            }
        }

        throw new NextStateNotFoundException($state);
    }

    private function actionable(string $class)
    {
        $instance = App::make($class);

        while ($instance instanceof Action) {
            $next = App::call([$instance, 'execute']);

            $instance = App::make($next);
        }

        throw_unless(
            $instance instanceof State,
            $instance::class.' does not implement '.State::class
        );

        return $instance::class;
    }

    private function terminating(State $state): bool
    {
        $reflected = new ReflectionClass($state);

        $attributes = $reflected->getAttributes(Terminate::class);

        return count($attributes) !== 0;
    }

    private function bail(Exception $exception): array
    {
        report($exception);

        $message = $this->exceptionHandler instanceof ExceptionHandler
            ? ($this->exceptionHandler)->handle($exception)
            : ($this->exceptionHandler)($exception);

        return [$message, true];
    }
}
