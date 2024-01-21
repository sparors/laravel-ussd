<?php

namespace Sparors\Ussd\Commands;

class StateCommand extends GeneratorCommand
{
    protected $type = 'USSD State';

    protected $description = 'Create a new USSD state class';

    protected $signature = 'ussd:state
                            {name : The name of the USSD State}
                            {--init : Create the class as an initial USSD state}
                            {--cont : Create the class as a continuing USSD state}
                            {--force : Create the class even if USSD state already exists}';

    protected function getStub()
    {
        if ($this->option('init')) {
            return __DIR__.'/../../stubs/state.init.stub';
        }

        if ($this->option('cont')) {
            return __DIR__.'/../../stubs/state.cont.stub';
        }

        return __DIR__.'/../../stubs/state.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->extendNamespace('States');
    }
}
