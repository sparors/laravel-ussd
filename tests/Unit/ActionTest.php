<?php

namespace Sparors\Ussd\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sparors\Ussd\Record;
use Sparors\Ussd\Tests\Dummy\FinishingState;
use Sparors\Ussd\Tests\Dummy\GrandAction;
use Sparors\Ussd\Tests\Dummy\PetitAction;
use Sparors\Ussd\Tests\Dummy\IntermediateState;

final class ActionTest extends TestCase
{
    public function test_action_can_run_without_any_dependency_injection()
    {
        $action = new PetitAction();

        $this->assertEquals(IntermediateState::class, $action->execute());
    }
}
