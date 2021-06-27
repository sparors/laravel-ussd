<?php

namespace Sparors\Ussd\Tests\Commands;

use Sparors\Ussd\Tests\TestCase;
use Illuminate\Support\Facades\File;

class StateCommandTest extends TestCase
{
    public function test_it_print_out_success_when_class_does_not_exists()
    {
        File::shouldReceive('exists')->once()->andReturn(false);
        File::shouldReceive('isDirectory')->once();
        File::shouldReceive('makeDirectory')->once();
        File::shouldReceive('put')->once()->andReturn(true);
        $this->artisan('ussd:state', ['name' => 'welcome'])
            ->expectsOutput('Welcome state created successfully')
            ->assertExitCode(0);
    }

    public function test_it_print_out_error_when_class_exists()
    {
        File::shouldReceive('exists')->once()->andReturn(true);
        $this->artisan('ussd:state', ['name' => 'welcome'])
            ->expectsOutput('File Already exists !')
            ->assertExitCode(0);
    }
}
