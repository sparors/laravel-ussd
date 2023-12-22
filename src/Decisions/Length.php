<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class Length implements Decision
{
    public function __construct(
        private int $length
    ) {
    }

    public function decide(string $actual): bool
    {
        return strlen($actual) === $this->length;
    }
}
