<?php

namespace Sparors\Ussd\Exceptions;

class UniqueIdentifierEmptyException extends UssdException
{
    public function __construct()
    {
        parent::__construct("Unique identifier (uid) can not be empty");
    }
}
