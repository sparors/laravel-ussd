<?php

namespace Sparors\Ussd\Commands;

class ResponseMakeCommand extends ResponseCommand
{
    protected $signature = 'make:ussd-response
                            {name : The name of the USSD Response}
                            {--force : Create the class even if USSD response already exists}';
}
