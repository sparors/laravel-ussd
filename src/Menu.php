<?php

namespace Sparors\Ussd;

use Sparors\Ussd\Traits\MenuBuilder;
use Stringable;

class Menu implements Stringable
{
    use MenuBuilder;

    public function __construct(
        private string $content
    ) {
    }

    public static function build(): static
    {
        return new static('');
    }

    public function append(self|callable $menu): static
    {
        if (is_callable($menu)) {
            $menu($append = new static(''));

            $menu = $append;
        }

        $this->content .= (string) $menu;

        return $this;
    }

    public function prepend(self|callable $menu): static
    {
        if (is_callable($menu)) {
            $menu($append = new static(''));

            $menu = $append;
        }

        $this->content = (string) $menu . $this->content;

        return $this;
    }

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

    public function __toString(): string
    {
        return $this->content;
    }
}
