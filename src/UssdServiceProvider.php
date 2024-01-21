<?php

namespace Sparors\Ussd;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Sparors\Ussd\Commands\ActionCommand;
use Sparors\Ussd\Commands\ActionMakeCommand;
use Sparors\Ussd\Commands\ConfiguratorCommand;
use Sparors\Ussd\Commands\ConfiguratorMakeCommand;
use Sparors\Ussd\Commands\DecisionCommand;
use Sparors\Ussd\Commands\DecisionMakeCommand;
use Sparors\Ussd\Commands\ExceptionHandlerCommand;
use Sparors\Ussd\Commands\ExceptionHandlerMakeCommand;
use Sparors\Ussd\Commands\ResponseCommand;
use Sparors\Ussd\Commands\ResponseMakeCommand;
use Sparors\Ussd\Commands\StateCommand;
use Sparors\Ussd\Commands\StateMakeCommand;

class UssdServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ussd.php', 'ussd');
    }

    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__.'/../config/ussd.php' => config_path('ussd.php'),
        ], 'ussd-config');

        $this->commands([
            StateCommand::class,
            StateMakeCommand::class,
            ActionCommand::class,
            ActionMakeCommand::class,
            ResponseCommand::class,
            ResponseMakeCommand::class,
            DecisionCommand::class,
            DecisionMakeCommand::class,
            ConfiguratorCommand::class,
            ConfiguratorMakeCommand::class,
            ExceptionHandlerCommand::class,
            ExceptionHandlerMakeCommand::class,
        ]);

        AboutCommand::add('USSD', [
            'Namespace' => config('ussd.namespace'),
            'Record Store' => config('ussd.record_store') ?? config('cache.default'),
        ]);
    }
}
