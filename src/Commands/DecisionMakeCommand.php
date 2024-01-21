<?php

namespace Sparors\Ussd\Commands;

class DecisionMakeCommand extends DecisionCommand
{
    protected $signature = 'make:ussd-decision
                            {name : The name of the USSD Decision}
                            {--force : Create the class even if USSD decision already exists}';
}
