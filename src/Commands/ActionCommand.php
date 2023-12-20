<?php

namespace Sparors\Ussd\Commands;

class ActionCommand extends GeneratorCommand
{
    protected $type = 'USSD Action';

    protected $description = 'Create a new USSD action class';

    protected $signature = 'ussd:action
                            {name : The name of the USSD Action}
                            {--force : Create the class even if USSD action already exists}';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/action.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->extendNamespace('Actions');
    }
}
