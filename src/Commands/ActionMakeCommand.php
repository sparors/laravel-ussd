<?php

namespace Sparors\Ussd\Commands;

class ActionMakeCommand extends ActionCommand
{
    protected $signature = 'make:ussd-action
                            {name : The name of the USSD Action}
                            {--force : Create the class even if USSD action already exists}';
}
