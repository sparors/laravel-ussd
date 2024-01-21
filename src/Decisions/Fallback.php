<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class Fallback implements Decision
{
    public function decide(string $actual): bool
    {
        return true;
    }
}
