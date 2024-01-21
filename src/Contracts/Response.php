<?php

namespace Sparors\Ussd\Contracts;

interface Response
{
    public function respond(string $message, bool $terminating): mixed;
}
