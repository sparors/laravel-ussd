<?php

namespace Sparors\Ussd\Exceptions;

class NoInitialStateProvided extends UssdException
{
    public function __construct()
    {
        parent::__construct('No initial state provided');
    }
}
