<?php

namespace Sparors\Ussd\Tests;

use Orchestra\Testbench\TestCase;
use Sparors\Ussd\State;

class StateTest extends TestCase
{
    public function testState()
    {
        $hello = new HelloState();
        $this->assertEquals('Hello World', $hello->render());

        $this->assertEquals(ByeState::class, $hello->next('1'));
    }
}
