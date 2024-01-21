<?php

namespace Sparors\Ussd\Decisions;

use Sparors\Ussd\Contracts\Decision;

class NotIn implements Decision
{
    private array $values;

    public function __construct(float|int|string ...$values)
    {
        $this->values = $values;
    }

    public function decide(string $actual): bool
    {
        return !in_array($actual, $this->values);
    }
}
