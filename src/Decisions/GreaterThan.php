<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class GreaterThan implements Decision
{
    public function __construct(
        private float|int|string $expected
    ) {
    }

    public function decide(string $actual): bool
    {
        return $actual > $this->expected;
    }
}
