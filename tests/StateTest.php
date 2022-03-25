<?php

namespace Sparors\Ussd\Tests;

use Orchestra\Testbench\TestCase;

class StateTest extends TestCase
{
    public function test_state_works_correctly()
    {
        $hello = new HelloState();
        
        $this->assertEquals('Hello World', $hello->render());

        $this->assertEquals(RunAction::class, $hello->next('1'));
    }
}
