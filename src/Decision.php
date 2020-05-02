<?php

namespace Sparors\Ussd;

class Decision
{
    /** @var boolean */
    protected $decided;

    protected $argument;

    /** @var string */
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

    /**
     * @return string
     */
    public function outcome()
    {
        return $this->output;
    }

    /**
     * @param mixed $argument
     * @param string $output
     * @param boolean $strict
     * @return Decision
     */
    public function equal($argument, $output, $strict = false)
    {
        return $this->setOutputForCondition(function () use ($argument, $strict) {
            if ($strict) {
                return $argument === $this->argument;
            }
            return $argument == $this->argument;
        }, $output);
    }

    /**
     * @param string $output
     * @return Decision
     */ 
    public function numeric($output)
    {
        return $this->setOutputForCondition(function () {
            return is_numeric($this->argument);
        }, $output);
    }

    /**
     * @param string $output
     * @return Decision
     */ 
    public function integer($output)
    {
        return $this->setOutputForCondition(function () {
            return is_integer($this->argument);
        }, $output);
    }

    /**
     * @param string $output
     * @return Decision
     */ 
    public function amount($output)
    {
        return $this->setOutputForCondition(function () {
            return preg_match("/^[0-9]+(?:\.[0-9]{1,2})?$/", $this->argument);
        }, $output);
    }

    /**
     * @param mixed $argument
     * @param string $output
     * @return Decision
     */ 
    public function length($argument, $output)
    {
        return $this->setOutputForCondition(function () use ($argument) {
            return strlen($this->argument) === $argument;
        }, $output);
    }

    /**
     * @param string $output
     * @return Decision
     */ 
    public function phoneNumber($output)
    {
        return $this->setOutputForCondition(function () {
            return preg_match("/^[0][0-9]{9}$/", $this->argument);
        }, $output);
    }

    /**
     * @param int $start
     * @param int $end
     * @param string $output
     * @return Decision
     */ 
    public function between($start, $end, $output)
    {
        return $this->setOutputForCondition(function () use ($start, $end) {
            return $this->argument >= $start && $this->argument <= $end;
        }, $output);
    }

    /**
     * @param array $array
     * @param string $output
     * @param bool $strict
     * @return Decision
     */ 
    public function in($array, $output, $strict = false)
    {
        return $this->setOutputForCondition(function () use ($array, $strict) {
            return in_array($array, $this->argument, $strict);
        }, $output);
    }

    /**
     * @param callable $function
     * @param string $output
     * @return Decision
     */ 
    public function custom($function, $output)
    {
        $func = function () use ($function) { return $function($this->argument); };
        return $this->setOutputForCondition($func, $output);
    }

    /**
     * @param string $output
     * @return Decision
     */ 
    public function any($output)
    {
        return $this->setOutputForCondition(function () {
            return true;
        }, $output);
    }
}
