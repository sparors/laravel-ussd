<?php

namespace Sparors\Ussd\Tests;

use Orchestra\Testbench\TestCase;
use Sparors\Ussd\Decision;

class DecisionTest extends TestCase
{
    public function testReturnsTheOutcome()
    {
        $decision = new Decision('argument');
        $this->assertNull($decision->outcome());
    }

    public function testCanCompareArgumentUsingEqual()
    {
        $decision = new Decision(1);
        $this->assertEquals('True', $decision->equal('1', 'True')
            ->outcome());
    }

    public function testCanCompareArgumentUsingStrictEqual()
    {
        $decision = new Decision(1);
        $this->assertNull($decision
            ->equal('1', 'True', true)->outcome());
    }

    public function testCanCompareArgumentUsingNumeric()
    {
        $decision = new Decision('1');
        $this->assertEquals('True', $decision->numeric('True')->outcome());
    }

    public function testCanCompareArgumentUsingInteger()
    {
        $decision = new Decision(1);
        $this->assertEquals('True', $decision->integer('True')->outcome());
    }

    public function testCanCompareArgumentUsingAmount()
    {
        $decision = new Decision(11.2);
        $this->assertEquals('True', $decision->amount('True')->outcome());
    }

    public function testCanCompareArgumentUsingLength()
    {
        $decision = new Decision('one');
        $this->assertEquals('True', $decision->length(3, 'True')
            ->outcome());
    }

    public function testCanCompareArgumentUsingPhoneNumber()
    {
        $decision = new Decision('0241212123');
        $this->assertEquals('True', $decision->phoneNumber('True')
            ->outcome());
    }

    public function testCanUseYourCustomCondition()
    {
        $decision = new Decision(['active' => true]);
        $this->assertEquals('Custom', $decision
            ->custom(function ($argument)  {
                return $argument['active'];
            }, 'Custom')->outcome());
    }

    public function testCanCompareArgumentBetweenTwoNumbers()
    {
        $decision = new Decision(3);
        $this->assertEquals('True', $decision->between(1, 10, 'True')->outcome());
    }

    public function testCanUseAnyWildCards()
    {
        $decision = new Decision('5');
        $this->assertEquals('True', $decision->any('True')->outcome());
    }

    public function testDecisionCanBeChain()
    {
        $decision = new Decision('45');
        $this->assertEquals('True', $decision
            ->phoneNumber('Phone')->any('True')->outcome());
    }

    public function testItIgnoresFollowingDecisionWhenConditionIsMet()
    {
        $decision = new Decision('1234');
        $this->assertEquals('First', $decision
            ->numeric('First')->any('Second')->outcome());
    }

    public function testItReturnsNullWhenNoConditionIsMet()
    {
        $decision = new Decision('super');
        $this->assertNull($decision->numeric('Numeric')
            ->phoneNumber('Phone')->equal('ama', 'True')->outcome());
    }
}
