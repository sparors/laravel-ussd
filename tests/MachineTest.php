<?php

namespace Sparors\Ussd\Tests;

use Exception;
use Sparors\Ussd\Machine;
use Sparors\Ussd\Facades\Ussd as UssdFacade;

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

    /** @dataProvider pass_configurator_as */
    public function test_it_can_make_use_of_a_configurator($operator, $configurator)
    {
        $machine = UssdFacade::machine()
            ->useConfigurator($configurator)
            ->setSessionId('1234')
            ->setInitialState(HelloState::class);

        $this->assertEquals(
            [
                'action' => 'input',
                'message' => 'Hello World',
                'operator' => $operator,
            ],
            $machine->run()
        );
    }

    public function pass_configurator_as()
    {
        return [
            ['Default', CogConfigurator::class],
            ['Dynamic', new CogConfigurator('Dynamic')],
        ];
    }

    public function test_invalid_configurator_throws_an_exception()
    {
        $this->expectException(Exception::class);

        UssdFacade::machine()
            ->useConfigurator(ByeState::class)
            ->setSessionId('1234')
            ->setInitialState(HelloState::class)
            ->run();
    }
}
