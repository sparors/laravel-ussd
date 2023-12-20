<?php

namespace Sparors\Ussd\Tests\Integration;

use Sparors\Ussd\Tests\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;

final class CommandTest extends TestCase
{
    public function test_action_command_print_out_success_when_class_does_not_exists()
    {
        File::shouldReceive('exists')->once();
        File::shouldReceive('isDirectory')->once();
        File::shouldReceive('makeDirectory')->once();
        File::shouldReceive('get')->once();
        File::shouldReceive('put')->once();

        $this->artisan('ussd:action', ['name' => 'Save'])
            ->expectsOutputToContain('USSD Action [app/Ussd/Actions/Save.php] created successfully.')
            ->assertExitCode(0);
    }

    public function test_action_command_print_out_error_when_class_exists()
    {
        File::shouldReceive('exists')->once()->andReturn(true);

        $this->artisan('ussd:action', ['name' => 'Save'])
            ->expectsOutputToContain('USSD Action already exists.')
            ->assertExitCode(0);
    }

    // #[DataProvider('data_available_make_commands')]
    /** @dataProvider data_available_make_commands */
    public function test_make_commands_are_available($command)
    {
        $this->artisan($command, ['name' => 'NameLess'])->assertExitCode(0);
    }

    public static function data_available_make_commands()
    {
        return [
            ['ussd:state'],
            ['make:ussd-state'],
            ['ussd:action'],
            ['make:ussd-action'],
            ['ussd:decision'],
            ['make:ussd-decision'],
            ['ussd:configurator'],
            ['make:ussd-configurator'],
        ];
    }

    public function test_state_command_print_out_success_when_class_does_not_exists()
    {
        File::shouldReceive('exists')->once();
        File::shouldReceive('isDirectory')->once();
        File::shouldReceive('makeDirectory')->once();
        File::shouldReceive('get')->once();
        File::shouldReceive('put')->once();

        $this->artisan('ussd:state', ['name' => 'Welcome'])
            ->expectsOutputToContain('USSD State [app/Ussd/States/Welcome.php] created successfully.')
            ->assertExitCode(0);
    }

    public function test_state_command_print_out_error_when_class_exists()
    {
        File::shouldReceive('exists')->once()->andReturn(true);

        $this->artisan('ussd:state', ['name' => 'Welcome'])
            ->expectsOutputToContain('USSD State already exists.')
            ->assertExitCode(0);
    }
}
