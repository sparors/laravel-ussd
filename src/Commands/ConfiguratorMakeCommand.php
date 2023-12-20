<?php

namespace Sparors\Ussd\Commands;

class ConfiguratorMakeCommand extends ConfiguratorCommand
{
    protected $signature = 'make:ussd-configurator
                            {name : The name of the USSD Configurator}
                            {--force : Create the class even if USSD configurator already exists}';
}
