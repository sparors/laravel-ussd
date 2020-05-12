<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\State;

class ByeState extends State
{
    protected $action = self::PROMPT;

    public function beforeRendering(): void
    {
        $this->menu->text('Bye World');
    }

    public function afterRendering(string $argument): void
    {

    }
}
