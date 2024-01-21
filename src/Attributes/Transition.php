<?php

namespace Sparors\Ussd\Attributes;

use Attribute;
use Sparors\Ussd\Contracts\Decision;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Transition
{
    public function __construct(
        public string $to,
        public array|Decision|string $match,
        public null|array|string $callback = null
    ) {
    }
}
