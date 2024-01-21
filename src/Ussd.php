<?php

namespace Sparors\Ussd;

use Closure;
use DateInterval;
use DateTimeInterface;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;
use Sparors\Ussd\Attributes\Paginate;
use Sparors\Ussd\Attributes\Terminate;
use Sparors\Ussd\Attributes\Transition;
use Sparors\Ussd\Attributes\Truncate;
use Sparors\Ussd\Contracts\Action;
use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Contracts\ExceptionHandler;
use Sparors\Ussd\Contracts\InitialAction;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Contracts\Response;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Exceptions\NextStateNotFoundException;
use Sparors\Ussd\Tests\PendingTest;
use Sparors\Ussd\Traits\Conditionable;
use Sparors\Ussd\Traits\WithPagination;

class Ussd
{
    use Conditionable;

    private const INIT = '__init__';
    private const HEAL = '__heal__';
    private const SPUR = '__spur__';
    private const HALT = '__halt__';
    private const CURB = '__curb__';
    private const LIVE = '__live__';

    private Context $context;
    private ?string $storeName;
    private int $continuingMode;
    private Closure|Response $response;
    private ?ContinueState $continuingState;
    private InitialAction|InitialState $initialState;
    private null|DateInterval|DateTimeInterface|int $continuingTtl;
    private Closure|ExceptionHandler $exceptionHandler;

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

    public static function build(?Context $context = null): static
    {
        return new static($context);
    }

    public static function test(
        InitialState|string $initialState,
        int $continuingMode = ContinuingMode::START,
        null|DateInterval|DateTimeInterface|int $continuingTtl = null,
        null|ContinueState|string $continuingState = null
    ): PendingTest {
        return new PendingTest($initialState, $continuingMode, $continuingTtl, $continuingState);
    }

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
            InvalidArgumentException::class,
            "Configurator should implement ".Configurator::class
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
            InvalidArgumentException::class,
            "Initial state should implement ".InitialState::class." or ".InitialAction::class
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
            InvalidArgumentException::class,
            "Invalid continuingMode"
        );

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

    public function useResponse(Closure|Response|string $response): static
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

    public function useExceptionHandler(Closure|ExceptionHandler|string $exceptionHandler): static
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

    public function useStore(string $storeName): static
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

        if (ContinuingMode::START !== $this->continuingMode) {
            /** @var Record */ $record =  App::make(Record::class);
            if ($terminating) {
                $record->forget(static::SPUR, true);
            } elseif ($spur = $record->get(static::SPUR, public: true)) {
                $record->set(static::SPUR, $spur, $this->continuingTtl, true);
            }
        }

        return $this->response instanceof Response
            ? ($this->response)->respond($message, $terminating)
            : ($this->response)($message, $terminating);
    }

    /** @return array{0: string, 1: bool} */
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

            if ($this->continuingState->confirm()->decide($this->context->input())) {
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
                static::INIT => true,
            ]);

            if (ContinuingMode::START !== $this->continuingMode) {
                $record->set(static::SPUR, $this->context->uid(), $this->continuingTtl, true);
            }
        }

        $state = App::make($nextState);

        /** @var Menu */ $menu = App::call([$state, 'render']);

        [$content, $more] = $this->limit($state, $menu);

        return [trim($content), $this->terminating($state, $more)];
    }

    private function next(State $state): string
    {
        $reflected = new ReflectionClass($state);

        $attributes = $reflected->getAttributes(Truncate::class);

        foreach ($attributes as $attribute) {
            $content = (string) App::call([$state, 'render']);

            $limitContent = $attribute->newInstance();

            if ($limitContent->limit > strlen($content)) {
                continue;
            }

            if (is_array($limitContent->more)) {
                $limitContent->more = new $limitContent->more[0](...array_slice($limitContent->more, 1));
            } elseif (is_string($limitContent->more)) {
                $limitContent->more = new $limitContent->more();
            }

            if ($limitContent->more->decide($this->context->input())) {
                $items = preg_split(
                    "/ÙÛÚ/",
                    wordwrap(
                        $content,
                        $limitContent->limit - (strlen($limitContent->end) + 1),
                        "ÙÛÚ",
                        true
                    )
                );

                /** @var Record */ $record =  App::make(Record::class);
                $limitId = Str::of($state::class)->replace('\\', '')->snake()->append('_limit')->value();
                $limit = $record->get($limitId, 1);

                if (count($items) > $limit) {
                    $record->set($limitId, $limit + 1);
                } else {
                    continue;
                }

                return $state::class;
            }
        }

        $attributes = $reflected->getAttributes(Paginate::class);

        foreach ($attributes as $attribute) {
            $paginate = $attribute->newInstance();

            foreach(['next', 'previous'] as $key) {
                if (is_array($paginate->{$key})) {
                    $paginate->{$key} = new $paginate->{$key}[0](...array_slice($paginate->{$key}, 1));
                } elseif (is_string($paginate->{$key})) {
                    $paginate->{$key} = new $paginate->{$key}();
                } elseif (is_null($paginate->{$key})) {
                    continue;
                }

                if ($paginate->{$key}->decide($this->context->input())) {
                    if (class_uses($state)[WithPagination::class] ?? null) {
                        /** @var WithPagination $state */
                        /** @var Record */ $record =  App::make(Record::class);
                        $pageId = Str::of($state::class)->replace('\\', '')->snake()->append('_page')->value();
                        $page = $record->get($pageId, 1);

                        if ('next' === $key && $state->hasNextPage()) {
                            $record->set($pageId, $page + 1);
                        } elseif ('previous' === $key && $state->hasPreviousPage()) {
                            $record->set($pageId, $page - 1);
                        } else {
                            continue;
                        }

                        $reflected = new ReflectionClass($state);

                        $attributes = $reflected->getAttributes(Truncate::class);

                        if (count($attributes) > 0) {
                            $limitId = Str::of($state::class)->replace('\\', '')->snake()->append('_limit')->value();
                            $limit = $record->get($limitId, 1);

                            if ($limit > 1) {
                                $record->set($limitId, 1);
                            }
                        }
                    }

                    if ($paginate->callback) {
                        if (is_array($paginate->callback) && is_string($paginate->callback[0])) {
                            $paginate->callback[0] = App::make($paginate->callback[0]);
                        }

                        App::call($paginate->callback);
                    }

                    return $state::class;
                }
            }
        }

        $attributes = $reflected->getAttributes(Transition::class);

        foreach($attributes as $attribute) {
            $transition = $attribute->newInstance();

            if (is_array($transition->match)) {
                $transition->match = new $transition->match[0](...array_slice($transition->match, 1));
            } elseif (is_string($transition->match)) {
                $transition->match = new $transition->match();
            }

            if ($transition->match->decide($this->context->input())) {
                if ($transition->callback) {
                    if (is_array($transition->callback) && is_string($transition->callback[0])) {
                        $transition->callback[0] = App::make($transition->callback[0]);
                    }

                    App::call($transition->callback);
                }

                return $transition->to;
            }
        }

        throw new NextStateNotFoundException($state);
    }

    private function actionable(string $class): string
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

    /** @return array{0: string, 1: bool} */
    private function limit(State $state, Menu $menu): array
    {
        $content = (string) $menu;

        $reflected = new ReflectionClass($state);

        $attributes = $reflected->getAttributes(Truncate::class);

        foreach ($attributes as $attribute) {
            $limitContent = $attribute->newInstance();

            if ($limitContent->limit > strlen($content)) {
                continue;
            }

            $items = preg_split(
                "/ÙÛÚ/",
                wordwrap(
                    $content,
                    $limitContent->limit - (strlen($limitContent->end) + 1),
                    "ÙÛÚ",
                    true
                )
            );

            /** @var Record */ $record =  App::make(Record::class);
            $limitId = Str::of($state::class)->replace('\\', '')->snake()->append('_limit')->value();
            $limit = $record->get($limitId, 1);

            return [
                count($items) > $limit
                    ? sprintf("%s\n%s", $items[$limit - 1], $limitContent->end)
                    : $items[$limit - 1],
                count($items) > $limit,
            ];
        }

        return [$content, false];
    }

    private function terminating(State $state, bool $more): bool
    {
        if ($more) {
            return false;
        }

        if (class_uses($state)[WithPagination::class] ?? null) {
            /** @var WithPagination $state */
            if ($state->hasNextPage()) {
                return false;
            }
        }

        $reflected = new ReflectionClass($state);

        $attributes = $reflected->getAttributes(Terminate::class);

        return 0 !== count($attributes);
    }

    /** @return array{0: string, 1: bool} */
    private function bail(Exception $exception): array
    {
        report($exception);

        $message = $this->exceptionHandler instanceof ExceptionHandler
            ? ($this->exceptionHandler)->handle($exception)
            : ($this->exceptionHandler)($exception);

        return [$message, true];
    }
}
