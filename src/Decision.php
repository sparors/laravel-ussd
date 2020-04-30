<?php

namespace Sparors\Ussd;

class Decision
{
    protected $decided;

    protected $argument;

    protected $output;

    public function __construct($argument = null)
    {
        $this->decided = false;
        $this->argument = $argument;
        $this->output = null;
    }

    private function guardAgainstReDeciding()
    {
        return !$this->decided;
    }

    private function setOutput($output)
    {
        $this->output = $output;
        $this->decided = true;
    }

    private function setOutputForCondition($condition, $output)
    {
        if ($this->guardAgainstReDeciding()) {
            if ($condition()) {
                $this->setOutput($output);
            }
        }
        return $this;
    }

    public function outcome()
    {
        return $this->output;
    }

    public function equal($argument, $output, $strict = false)
    {
        return $this->setOutputForCondition(function () use ($argument, $strict) {
            if ($strict) {
                return $argument === $this->argument;
            }
            return $argument == $this->argument;
        }, $output);
    }

    public function numeric($output)
    {
        return $this->setOutputForCondition(function () {
            return is_numeric($this->argument);
        }, $output);
    }

    public function integer($output)
    {
        return $this->setOutputForCondition(function () {
            return is_integer($this->argument);
        }, $output);
    }

    public function amount($output)
    {
        return $this->setOutputForCondition(function () {
            return preg_match("/^[0-9]+(?:\.[0-9]{1,2})?$/", $this->argument);
        }, $output);
    }

    public function length($argument, $output)
    {
        return $this->setOutputForCondition(function () use ($argument) {
            return strlen($this->argument) === $argument;
        }, $output);
    }

    public function phoneNumber($output)
    {
        return $this->setOutputForCondition(function () {
            return preg_match("/^[0][0-9]{9}$/", $this->argument);
        }, $output);
    }

    public function between($start, $end, $output)
    {
        return $this->setOutputForCondition(function () use ($start, $end) {
            return $this->argument >= $start && $this->argument <= $end;
        }, $output);
    }

    public function in($array, $output, $strict = false)
    {
        return $this->setOutputForCondition(function () use ($array, $strict) {
            return in_array($array, $this->argument, $strict);
        }, $output);
    }

    public function custom($function, $output)
    {
        $func = function () use ($function) { return $function($this->argument); };
        return $this->setOutputForCondition($func, $output);
    }

    public function any($output)
    {
        return $this->setOutputForCondition(function () {
            return true;
        }, $output);
    }
}
