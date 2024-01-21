<?php

namespace Sparors\Ussd\Commands;

class StateMakeCommand extends StateCommand
{
    protected $signature = 'make:ussd-state
                            {name : The name of the USSD State}
                            {--init : Create the class as an initial USSD state}
                            {--cont : Create the class as a continuing USSD state}
                            {--force : Create the class even if USSD state already exists}';
}
