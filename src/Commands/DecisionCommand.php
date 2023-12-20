<?php

namespace Sparors\Ussd\Commands;

class DecisionCommand extends GeneratorCommand
{
    protected $type = 'USSD Decision';

    protected $description = 'Create a new USSD decision class';

    protected $signature = 'ussd:decision
                            {name : The name of the USSD Decision}
                            {--force : Create the class even if USSD decision already exists}';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/decision.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->extendNamespace('Decisions');
    }
}
