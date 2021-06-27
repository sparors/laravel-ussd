<?php

namespace Sparors\Ussd\Tests\Facades;

use Sparors\Ussd\Facades\Ussd as UssdFacade;
use Sparors\Ussd\Machine;
use Sparors\Ussd\Tests\TestCase;
use Sparors\Ussd\Ussd;

class UssdTest extends TestCase
{
    public function test_it_injects_machine_using_the_facade()
    {
        $this->assertInstanceOf(Machine::class, UssdFacade::machine());
    }

    public function test_it_injects_machine_using_the_service_container()
    {
        $this->assertInstanceOf(Ussd::class, app('ussd'));
    }
}
