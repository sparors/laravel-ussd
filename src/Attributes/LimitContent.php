<?php

namespace Sparors\Ussd\Attributes;

use Attribute;
use Sparors\Ussd\Contracts\Decision;

#[Attribute(Attribute::TARGET_CLASS)]
final class LimitContent
{
    public function __construct(
        public string|array|Decision $more,
        public int $characters,
        public string $moreText,
    ) { }
}
