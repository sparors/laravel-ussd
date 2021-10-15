<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Decision;
use PHPUnit\Framework\TestCase;

class DecisionTest extends TestCase
{
    public function test_it_returns_the_outcome()
    {
        $decision = new Decision('argument');
        $this->assertNull($decision->outcome());
    }

    public function test_it_can_compare_argument_using_equal()
    {
        $decision = new Decision(1);
        $this->assertEquals('True', $decision->equal('1', 'True')
            ->outcome());
    }

    public function test_it_can_compare_argument_using_strict_equal()
    {
        $decision = new Decision(1);
        $this->assertNull($decision
            ->equal('1', 'True', true)->outcome());
    }

    public function test_it_can_compare_argument_using_numeric()
    {
        $decision = new Decision('1');
        $this->assertEquals('True', $decision->numeric('True')->outcome());
    }

    public function test_it_can_compare_argument_using_integer()
    {
        $decision = new Decision(1);
        $this->assertEquals('True', $decision->integer('True')->outcome());
    }

    public function test_it_can_compare_argument_using_amount()
    {
        $decision = new Decision(11.2);
        $this->assertEquals('True', $decision->amount('True')->outcome());
    }

    public function test_it_can_compare_argument_using_length()
    {
        $decision = new Decision('one');
        $this->assertEquals('True', $decision->length(3, 'True')
            ->outcome());
    }

    public function test_it_can_compare_argument_using_phone_number()
    {
        $decision = new Decision('0241212123');
        $this->assertEquals('True', $decision->phoneNumber('True')
            ->outcome());
    }

    public function test_it_can_use_your_custom_conditional_logic()
    {
        $decision = new Decision(['active' => true]);
        $this->assertEquals('Custom', $decision
            ->custom(function ($argument) {
                return $argument['active'];
            }, 'Custom')->outcome());
    }

    public function test_it_can_compare_argument_between_two_numbers()
    {
        $decision = new Decision(3);
        $this->assertEquals('True', $decision->between(1, 10, 'True')->outcome());
    }

    public function test_it_can_compare_argument_with_values_in_an_array()
    {
        $decision = new Decision('second');
        $this->assertEquals('True', $decision->in(['first', 'second', 'third'], 'True')->outcome());
    }

    public function test_it_can_compare_argument_with_values_in_an_array_strictly()
    {
        $decision = new Decision(2);
        $this->assertEquals('True', $decision->in(['1', 2, '3'], 'True', true)->outcome());
    }

    public function test_it_can_use_any_wild_cards()
    {
        $decision = new Decision('5');
        $this->assertEquals('True', $decision->any('True')->outcome());
    }

    public function test_decision_can_be_chain()
    {
        $decision = new Decision('45');
        $this->assertEquals('True', $decision
            ->phoneNumber('Phone')->any('True')->outcome());
    }

    public function test_it_ignores_following_decision_when_condition_is_met()
    {
        $decision = new Decision('1234');
        $this->assertEquals('First', $decision
            ->numeric('First')->any('Second')->outcome());
    }

    public function test_it_returns_null_when_no_condition_is_met()
    {
        $decision = new Decision('super');
        $this->assertNull($decision->numeric('Numeric')
            ->phoneNumber('Phone')->equal('ama', 'True')->outcome());
    }
}
