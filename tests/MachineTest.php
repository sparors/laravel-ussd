<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Machine;
use Orchestra\Testbench\TestCase;

class MachineTest extends TestCase
{
    public function test_it_runs_successfully()
    {
        $machine = (new Machine())->setSessionId('1234')
            ->setInput('1')
            ->setInitialState(HelloState::class)
            ->setStore('array');

        $this->assertEquals(
            [
                'message' => 'Hello World',
                'action' => 'input'
            ],
            $machine->run()
        );

        $machine->setInput('2');

        $this->assertEquals(
            [
                'message' => 'Bye World',
                'action' => 'prompt'
            ],
            $machine->run()
        );
    }

    public function test_initial_state_can_be_a_callable()
    {
        $machine = (new Machine())->setSessionId('1234')
            ->setInput('1')
            ->setInitialState(function () {
                return HelloState::class;
            })
            ->setStore('array');

        $this->assertEquals(
            [
                'message' => 'Hello World',
                'action' => 'input'
            ],
            $machine->run()
        );
    }
}
