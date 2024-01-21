<?php

namespace Sparors\Ussd\Exceptions;

class ActiveStateNotFoundException extends UssdException
{
    public function __construct()
    {
        parent::__construct('Active state not found. This may indicate session has ended');
    }
}
