<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class Between implements Decision
{
    public function __construct(
        private string|int|float $start,
        private string|int|float $end
    ) { }

    public function decide(string $actual): bool
    {
        return $actual >= $this->start && $actual <= $this->end;
    }
}
