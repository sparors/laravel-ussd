<?php

namespace Sparors\Ussd\Attributes;

use Attribute;
use Sparors\Ussd\Contracts\Decision;

#[Attribute(Attribute::TARGET_CLASS)]
final class Paginate
{
    public function __construct(
        public array|Decision|string $next,
        public null|array|Decision|string $previous = null,
        public null|array|string $callback = null
    ) {
    }
}
