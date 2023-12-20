<?php

namespace Sparors\Ussd\Commands;

class StateMakeCommand extends StateCommand
{
    protected $signature = 'make:ussd-state
                            {name : The name of the USSD State}
                            {--force : Create the class even if USSD state already exists}';
}
