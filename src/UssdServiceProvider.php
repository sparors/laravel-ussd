<?php

namespace Sparors\Ussd;

use Illuminate\Support\ServiceProvider;
use Sparors\Ussd\Commands\StateCommand;
use Sparors\Ussd\Commands\ActionCommand;
use Sparors\Ussd\Commands\DecisionCommand;
use Sparors\Ussd\Commands\ResponseCommand;
use Sparors\Ussd\Commands\StateMakeCommand;
use Sparors\Ussd\Commands\ActionMakeCommand;
use Sparors\Ussd\Commands\ConfiguratorCommand;
use Sparors\Ussd\Commands\DecisionMakeCommand;
use Sparors\Ussd\Commands\ResponseMakeCommand;
use Illuminate\Foundation\Console\AboutCommand;
use Sparors\Ussd\Commands\ConfiguratorMakeCommand;

class UssdServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ussd.php', 'ussd');
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
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
        ]);

        AboutCommand::add('USSD', [
            'Namespace' => config('ussd.namespace'),
            'Record Store' => config('ussd.record_store') ?? config('cache.default'),
        ]);
    }
}
