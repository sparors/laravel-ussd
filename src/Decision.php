<?php

namespace Sparors\Ussd;

class Decision
{
    /** @var bool */
    protected $decided;

    /** @var mixed */
    protected $argument;

    /** @var string|null */
    protected $output;

    public function __construct($argument = null)
    {
        $this->decided = false;
        $this->argument = $argument;
        $this->output = null;
    }

    protected function guardAgainstReDeciding(): bool
    {
        return !$this->decided;
    }

    protected function setOutput($output): void
    {
        $this->output = $output;
        $this->decided = true;
    }

    protected function setOutputForCondition($condition, $output): self
    {
        if ($this->guardAgainstReDeciding()) {
            if ($condition()) {
                $this->setOutput($output);
            }
        }

        return $this;
    }

    public function outcome(): ?string
    {
        return $this->output;
    }

    public function equal($argument, string $output, bool $strict = false): self
    {
        return $this->setOutputForCondition(
            function () use ($argument, $strict) {
                if ($strict) {
                    return $argument === $this->argument;
                }
                return $argument == $this->argument;
            },
            $output
        );
    }

    public function numeric(string $output): self
    {
        return $this->setOutputForCondition(
            function () {
                return is_numeric($this->argument);
            },
            $output
        );
    }

    public function integer(string $output): self
    {
        return $this->setOutputForCondition(
            function () {
                return is_integer($this->argument);
            },
            $output
        );
    }

    public function amount(string $output): self
    {
        return $this->setOutputForCondition(
            function () {
                return preg_match(
                    "/^[0-9]+(?:\.[0-9]{1,2})?$/",
                    $this->argument
                );
            },
            $output
        );
    }

    public function length($argument, string $output): self
    {
        return $this->setOutputForCondition(
            function () use ($argument) {
                return strlen($this->argument) === $argument;
            },
            $output
        );
    }

    public function phoneNumber(string $output): self
    {
        return $this->setOutputForCondition(
            function () {
                return preg_match("/^[0][0-9]{9}$/", $this->argument);
            },
            $output
        );
    }

    public function between(int $start, int $end, string $output): self
    {
        return $this->setOutputForCondition(
            function () use ($start, $end) {
                return $this->argument >= $start && $this->argument <= $end;
            },
            $output
        );
    }

    public function in(array $array, string $output, bool $strict = false): self
    {
        return $this->setOutputForCondition(
            function () use ($array, $strict) {
                return in_array($this->argument, $array, $strict);
            },
            $output
        );
    }

    public function custom(callable $function, string $output): self
    {
        $func = function () use ($function) {
            return $function($this->argument);
        };

        return $this->setOutputForCondition($func, $output);
    }

    public function any(string $output): self
    {
        return $this->setOutputForCondition(
            function () {
                return true;
            },
            $output
        );
    }
}
