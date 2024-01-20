<?php

namespace Sparors\Ussd\Tests\Integration;

use Sparors\Ussd\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class CommandTest extends TestCase
{
    /** @dataProvider data_available_make_commands */
    public function test_action_command_print_out_success_when_class_does_not_exists($command, $class)
    {
        File::shouldReceive('exists')->once();
        File::shouldReceive('isDirectory')->once();
        File::shouldReceive('makeDirectory')->once();
        File::shouldReceive('get')->once();
        File::shouldReceive('put')->once();

        $this->artisan($command, ['name' => $class])
            ->expectsOutputToContain($class)
            ->assertExitCode(0);
    }

    /** @dataProvider data_available_make_commands */
    public function test_action_command_print_out_error_when_class_exists($command, $class)
    {
        File::shouldReceive('exists')->once()->andReturn(true);

        $this->artisan($command, ['name' => $class])
            ->expectsOutputToContain('already exists.')
            ->assertExitCode(0);
    }

    public static function data_available_make_commands()
    {
        return [
            ['ussd:state', 'WelcomeState'],
            ['make:ussd-state', 'WelcomeState'],
            ['ussd:action', 'MenuAction'],
            ['make:ussd-action', 'MenuAction'],
            ['ussd:decision', 'StrictEqual'],
            ['make:ussd-decision', 'StrictEqual'],
            ['ussd:configurator', 'DynamicConfigurator'],
            ['make:ussd-configurator', 'DynamicConfigurator'],
            ['ussd:exception-handler', 'CatchExceptionHandler'],
            ['make:ussd-exception-handler', 'CatchExceptionHandler'],
        ];
    }
}
