<?php

namespace Sparors\Ussd\Tests;

use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function test_it_runs_successfully()
    {
        $action = new RunAction();
        $this->assertEquals(ByeState::class, $action->run());
    }
}
