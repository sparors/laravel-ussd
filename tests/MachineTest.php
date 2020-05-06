<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\State;
use Sparors\Ussd\Machine;
use Orchestra\Testbench\TestCase;

class MachineTest extends TestCase
{
    public function testRun()
    {
        $machine = (new Machine())->setSessionId('1234')
            ->setInput('1')
            ->setInitialState(HelloState::class);
        
        $this->assertEquals(
            [
                'message' => 'Hello World',
                'code' => 1
            ],
            $machine->run()
        );

        $machine->setInput('2');

        $this->assertEquals(
            [
                'message' => 'Bye World',
                'code' => 2
            ],
            $machine->run()
        );
    }
}
