<?php

namespace Sparors\Ussd\Tests\Commands;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;
use Sparors\Ussd\UssdServiceProvider;

class TestStateCommand extends TestCase
{
    /**
     * Tell Testbench to use this package.
     *
     * @param $app
     *
     * @return array
     */
    public function getPackageProviders($app)
    {
        return [UssdServiceProvider::class];
    }
    
    public function testItPrintOutSuccessWhenClassDoesNotExists()
    {
        File::shouldReceive('exists')->once()->andReturn(false);
        File::shouldReceive('isDirectory')->once();
        File::shouldReceive('makeDirectory')->once();
        File::shouldReceive('put')->once()->andReturn(true);
        $this->artisan('ussd:state', ['name' => 'welcome'])
            ->expectsOutput('Welcome state created successfully')
            ->assertExitCode(0);
    }

    public function testItPrintOutErrorWhenClassExists()
    {
        File::shouldReceive('exists')->once()->andReturn(true);
        $this->artisan('ussd:state', ['name' => 'welcome'])
            ->expectsOutput('File Already exists !')
            ->assertExitCode(0);
    }
}
