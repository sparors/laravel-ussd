<?php

namespace Sparors\Ussd\Commands;

class ConfiguratorCommand extends GeneratorCommand
{
    protected $type = 'USSD Configurator';

    protected $description = 'Create a new USSD configurator class';

    protected $signature = 'ussd:configurator
                            {name : The name of the USSD Configurator}
                            {--force : Create the class even if USSD configurator already exists}';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/configurator.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->extendNamespace('Configurators');
    }
}
