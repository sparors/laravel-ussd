<?php

namespace Sparors\Ussd\Exceptions;

class GlobaldentifierEmptyException extends UssdException
{
    public function __construct()
    {
        parent::__construct("Global identifier (gid) can not be empty");
    }
}
