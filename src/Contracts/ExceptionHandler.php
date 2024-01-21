<?php

namespace Sparors\Ussd\Contracts;

use Exception;

interface ExceptionHandler
{
    public function handle(Exception $exception): string;
}
