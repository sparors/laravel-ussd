<?php

namespace Sparors\Ussd\Attributes;

use Attribute;
use Sparors\Ussd\Contracts\Decision;

#[Attribute(Attribute::TARGET_CLASS)]
final class Paginate
{
    public function __construct(
        public string|array|Decision $next,
        public null|string|array|Decision $previous = null,
        public null|string|array $callback = null
    ) { }
}
