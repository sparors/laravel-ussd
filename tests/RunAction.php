<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Action;

/** @internal */
class RunAction extends Action
{
    public function run(): string
    {
        return ByeState::class;
    }
}
