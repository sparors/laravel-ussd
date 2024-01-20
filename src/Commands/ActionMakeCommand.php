<?php

namespace Sparors\Ussd\Commands;

class ActionMakeCommand extends ActionCommand
{
    protected $signature = 'make:ussd-action
                            {name : The name of the USSD Action}
                            {--init : Create the class as the initial USSD action}
                            {--force : Create the class even if USSD action already exists}';
}
