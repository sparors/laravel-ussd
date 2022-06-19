<?php

namespace Sparors\Ussd;

use Illuminate\Support\ServiceProvider;
use Sparors\Ussd\Commands\ActionCommand;
use Sparors\Ussd\Commands\StateCommand;

class UssdServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
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
        // Register the service the package provides.
        $this->app->singleton('ussd', function ($app) {
            return new Ussd($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ussd'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/ussd.php' => config_path('ussd.php'),
        ], 'ussd-config');

        // Registering package commands.
        $this->commands([
            StateCommand::class,
            ActionCommand::class,
        ]);
    }
}
