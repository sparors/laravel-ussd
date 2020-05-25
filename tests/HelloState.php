<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\State;

/** @internal */
class HelloState extends State
{
    public function beforeRendering(): void
    {
        $this->menu->text('Hello World');
    }

    public function afterRendering(string $argument): void
    {
        $this->decision->any(RunAction::class);
    }
}
