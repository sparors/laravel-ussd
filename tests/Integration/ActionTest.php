<?php

namespace Sparors\Ussd\Tests\Integration;

use Sparors\Ussd\Record;
use Sparors\Ussd\Tests\TestCase;
use Sparors\Ussd\Tests\Dummy\GrandAction;
use Sparors\Ussd\Tests\Dummy\FinishingState;

final class ActionTest extends TestCase
{
    public function test_action_can_run_with_dependency_injection()
    {
        $record = new Record('array', '1234', 'abcd');
        $action = new GrandAction();

        $this->assertEquals(FinishingState::class, $action->execute($record));
    }
}
