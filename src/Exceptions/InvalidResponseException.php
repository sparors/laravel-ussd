<?php

namespace Sparors\Ussd\Exceptions;

use Sparors\Ussd\Contracts\Response;

class InvalidResponseException extends UssdException
{
    public function __construct(string $response)
    {
        parent::__construct("Invalid response, {$response} should implement ".Response::class." or be a closure");
    }
}
