<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Action;

class RunAction extends Action
{
    public function run(): string
    {
        return ByeState::class;
    }
}
