<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class Regex implements Decision
{
    public function __construct(
        private string $pattern
    ) { }

    public function decide(string $actual): bool
    {
        return preg_match($this->pattern, $actual);
    }
}
