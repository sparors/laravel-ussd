<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class Between implements Decision
{
    public function __construct(
        private float|int|string $start,
        private float|int|string $end
    ) {
    }

    public function decide(string $actual): bool
    {
        return $actual >= $this->start && $actual <= $this->end;
    }
}
