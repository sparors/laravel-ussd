<?php

namespace Sparors\Ussd\Exceptions;

use Sparors\Ussd\Contracts\ExceptionHandler;

class InvalidExceptionHandlerException extends UssdException
{
    public function __construct(string $exceptionHandler)
    {
        parent::__construct("Invalid exception handler, {$exceptionHandler} should implement ".ExceptionHandler::class." or be a closure");
    }
}
