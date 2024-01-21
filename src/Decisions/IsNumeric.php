<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class IsNumeric implements Decision
{
    public function decide(string $actual): bool
    {
        return is_numeric($actual);
    }
}
