<?php

namespace Sparors\Ussd\Tests;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonSerializable;
use PHPUnit\Framework\Assert;
use ReflectionFunction;
use Sparors\Ussd\Context;
use Sparors\Ussd\Contracts\Configurator;
use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Contracts\ExceptionHandler;
use Sparors\Ussd\Contracts\InitialAction;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Contracts\Response;
use Sparors\Ussd\Record;
use Sparors\Ussd\Ussd;

class Testing
{
    private bool $switched;
    private array $actors;

    public function __construct(
        private InitialAction|InitialState|string $initialState,
        private int $continuingMode,
        private null|DateInterval|DateTimeInterface|int $continuingTtl,
        private null|ContinueState|string $continuingState,
        private ?string $storeName,
        private array $additional,
        private array $uses,
        private string $actor,
        private string $startInput
    ) {
        $this->actors[$actor] = [Str::random(), Str::random(), ''];
        $this->dispatch($startInput);
    }

    public function assertContextHas(string $key, $value = null): static
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

    public function assertContextMissing(string $key): static
    {
        $this->preventStaleAssertion();

        $item = App::get(Context::class)->get($key);

        Assert::assertFalse(
            isset($item),
            "Context has unexpected key [{$key}]."
        );

        return $this;
    }

    public function assertRecordHas(string $key, $value = null): static
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

    public function assertRecordMissing(string $key): static
    {
        $this->preventStaleAssertion();

        $item = App::get(Record::class)->get($key);

        Assert::assertFalse(
            isset($item),
            "Record has unexpected key [{$key}]."
        );

        return $this;
    }

    public function assertSee(string $value): static
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

    public function actingAs(string $key): static
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

    public function wait(DateInterval|DateTimeInterface|int $ttl): static
    {
        if ($ttl instanceof DateTimeInterface) {
            $ttl = Carbon::now()->diffInSeconds($ttl);
        }

        if ($ttl instanceof DateInterval) {
            $ttl = Carbon::now()->diffInSeconds(Carbon::now()->add($ttl));
        }

        Carbon::setTestNow(Carbon::now()->addSeconds($ttl));

        return $this;
    }

    public function timeout(null|DateInterval|DateTimeInterface|int $ttl = null, ?string $input = null): static
    {
        if ($ttl) {
            $this->wait($ttl);
        }

        $this->actors[$this->actor][0] = Str::random();

        $this->dispatch($input ?? $this->startInput);

        return $this;
    }

    public function input(string $input): static
    {
        $this->dispatch($input);

        return $this;
    }

    private function dispatch(string $input): void
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

    private function applyUses(Ussd $ussd): void
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

            throw new InvalidArgumentException('Invalid use provided.');
        }
    }

    private function preventStaleAssertion(): void
    {
        if ($this->switched) {
            $caller = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
            Assert::fail("Call 'input' before '{$caller}' after 'actingAs', or assert before switching to previous users.");
        }
    }
}
