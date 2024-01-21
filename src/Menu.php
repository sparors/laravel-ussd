<?php

namespace Sparors\Ussd;

use Sparors\Ussd\Traits\Conditionable;
use Sparors\Ussd\Traits\MenuBuilder;
use Stringable;

class Menu implements Stringable
{
    use Conditionable;
    use MenuBuilder;

    public function __construct(
        private string $content
    ) {
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public static function build(): static
    {
        return new static('');
    }

    public function append(callable|self $menu): static
    {
        if (is_callable($menu)) {
            $menu($append = new static(''));

            $menu = $append;
        }

        $this->content .= (string) $menu;

        return $this;
    }

    public function prepend(callable|self $menu): static
    {
        if (is_callable($menu)) {
            $menu($append = new static(''));

            $menu = $append;
        }

        $this->content = (string) $menu . $this->content;

        return $this;
    }
}
