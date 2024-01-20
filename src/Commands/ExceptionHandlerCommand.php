<?php

namespace Sparors\Ussd\Commands;

class ExceptionHandlerCommand extends GeneratorCommand
{
    protected $type = 'USSD Exception Handler';

    protected $description = 'Create a new USSD exception handler class';

    protected $signature = 'ussd:exception-handler
                            {name : The name of the USSD Exception Handler}
                            {--force : Create the class even if USSD exception handler already exists}';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/exception-handler.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->extendNamespace('ExceptionHandlers');
    }
}
