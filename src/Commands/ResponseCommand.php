<?php

namespace Sparors\Ussd\Commands;

class ResponseCommand extends GeneratorCommand
{
    protected $type = 'USSD Response';

    protected $description = 'Create a new USSD response class';

    protected $signature = 'ussd:response
                            {name : The name of the USSD Response}
                            {--force : Create the class even if USSD response already exists}';

    protected function getStub()
    {
        return __DIR__.'/../../stubs/response.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->extendNamespace('Responses');
    }
}
