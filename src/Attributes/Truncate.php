<?php

namespace Sparors\Ussd\Attributes;

use Attribute;
use Sparors\Ussd\Contracts\Decision;

#[Attribute(Attribute::TARGET_CLASS)]
final class Truncate
{
    public function __construct(
        public int $limit,
        public string $end,
        public string|array|Decision $more
    ) {
    }
}
