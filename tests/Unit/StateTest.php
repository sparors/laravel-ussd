<?php

namespace Sparors\Ussd\Tests\Unit;

use Orchestra\Testbench\TestCase;
use Sparors\Ussd\Tests\Dummy\BeginningState;

final class StateTest extends TestCase
{
    public function test_state_can_render_with_any_dependency_injection()
    {
        $state = new BeginningState();

        $this->assertEquals('In the beginning...', $state->render());
    }
}
