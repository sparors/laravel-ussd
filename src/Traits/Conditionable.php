<?php

namespace Sparors\Ussd\Traits;

trait Conditionable
{
    public function when(bool $value, callable $callback, callable $fallback = null): static
    {
        if ($value) {
            $callback($this);
        } elseif ($fallback) {
            $fallback($this);
        }

        return $this;
    }

    public function unless(bool $value, callable $callback, callable $fallback = null): static
    {
        return $this->when(!$value, $callback, $fallback);
    }
}
