<?php

namespace Sparors\Ussd\Commands;

class ExceptionHandlerMakeCommand extends ExceptionHandlerCommand
{
    protected $signature = 'make:ussd-exception-handler
                            {name : The name of the USSD Exception Handler}
                            {--force : Create the class even if USSD exception handler already exists}';
}
