<?php

namespace Sparors\Ussd\Tests;

use Orchestra\Testbench\TestCase;
use Sparors\Ussd\State;

/** @internal */
class ActionTest extends TestCase
{
    public function testAction()
    {
        $action = new RunAction();
        $this->assertEquals(ByeState::class, $action->run());
    }
}