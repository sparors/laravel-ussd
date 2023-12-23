<?php

namespace Sparors\Ussd\Tests;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use JsonSerializable;
use Sparors\Ussd\Contracts\Response;
use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Contracts\ExceptionHandler;
use PHPUnit\Framework\Assert;
use ReflectionFunction;
use Sparors\Ussd\Context;
use Sparors\Ussd\Contracts\InitialAction;
use Sparors\Ussd\Record;
use Sparors\Ussd\Ussd;

class Testing
{
    private bool $switched;
    private array $actors;

    public function __construct(
        private string|InitialState|InitialAction $initialState,
        private int $continuingMode,
        private null|int|DateInterval|DateTimeInterface $continuingTtl,
        private null|string|ContinueState $continuingState,
        private ?string $storeName,
        private array $additional,
        private array $uses,
        private string $actor
    ) {
        $this->actors[$actor] = [Str::random(), Str::random(), ''];
        $this->dispatch('');
    }

    public function assertContextHas(string $key, $value = null)
    {
        $this->preventStaleAssertion();

        $item = App::get(Context::class)->get($key);

        if (is_null($value)) {
            Assert::assertTrue(
                isset($item),
                "Context is missing expected key [{$key}]."
            );
        } elseif ($value instanceof Closure) {
            Assert::assertTrue($value($item));
        } else {
            Assert::assertEquals($value, $item);
        }

        return $this;
    }

    public function assertContextMissing(string $key)
    {
        $this->preventStaleAssertion();

        $item = App::get(Context::class)->get($key);

        Assert::assertFalse(
            isset($item),
            "Context has unexpected key [{$key}]."
        );

        return $this;
    }

    public function assertRecordHas(string $key, $value = null)
    {
        $this->preventStaleAssertion();

        $item = App::get(Record::class)->get($key);

        if (is_null($value)) {
            Assert::assertTrue(
                isset($item),
                "Record is missing expected key [{$key}]."
            );
        } elseif ($value instanceof Closure) {
            Assert::assertTrue($value($item));
        } else {
            Assert::assertEquals($value, $item);
        }

        return $this;
    }

    public function assertRecordMissing(string $key)
    {
        $this->preventStaleAssertion();

        $item = App::get(Record::class)->get($key);

        Assert::assertFalse(
            isset($item),
            "Record has unexpected key [{$key}]."
        );

        return $this;
    }

    public function assertSee(string $value)
    {
        $item = $this->actors[$this->actor][2];

        if (is_object($item) || is_array($item) || $item instanceof JsonSerializable) {
            $item = json_encode($item);
        }

        if ($item instanceof HttpResponse && is_string($item->getContent())) {
            $item = $item->getContent();
        }

        if (is_string($item)) {
            Assert::assertStringContainsString($value, $item);
        } else {
            Assert::fail('Result for ussd could not be converted to string.');
        }

        return $this;
    }

    public function actingAs(string $key)
    {
        $this->actor = $key;

        if (!isset($this->actors[$key])) {
            $this->actors[$key] = [Str::random(), Str::random(), ''];
            $this->dispatch('');
        } else {
            $this->switched = true;
        }

        return $this;
    }

    public function timeout()
    {
        $this->actors[$this->actor][0] = Str::random();

        $this->dispatch('');

        return $this;
    }

    public function input(string $input)
    {
        $this->dispatch($input);

        return $this;
    }

    private function dispatch(string $input)
    {
        [$uid, $gid] = $this->actors[$this->actor];

        $context = Context::create($uid, $gid, $input)->with($this->additional);
        $ussd = Ussd::build($context)
                    ->useInitialState($this->initialState)
                    ->useContinuingState($this->continuingMode, $this->continuingTtl, $this->continuingState);

        $this->applyUses($ussd);

        $this->actors[$this->actor][2] = $ussd->run();

        $this->switched = false;
    }

    private function applyUses(Ussd $ussd)
    {
        foreach ($this->uses as $use) {
            if (is_string($use)) {
                $use = App::make($use);
            }

            if ($use instanceof Configurator) {
                $ussd->useConfigurator($use);
                continue;
            }

            if ($use instanceof Response) {
                $ussd->useResponse($use);
                continue;
            }

            if ($use instanceof ExceptionHandler) {
                $ussd->useExceptionHandler($use);
                continue;
            }

            if ($use instanceof Closure) {
                $parameters = (new ReflectionFunction($use))->getNumberOfParameters();

                if (1 === $parameters) {
                    $ussd->useExceptionHandler($use);
                    continue;
                }

                if (2 === $parameters) {
                    $ussd->useResponse($use);
                    continue;
                }
            }

            throw new \InvalidArgumentException('Invalid use provided.');
        }

        return $this;
    }

    private function preventStaleAssertion(): void
    {
        if ($this->switched) {
            $caller = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
            Assert::fail("Call 'input' before '{$caller}' after 'actingAs', or assert before switching to previous users.");
        }
    }
}
