<?php

namespace Sparors\Ussd;

use Sparors\Ussd\Exceptions\GlobaldentifierEmptyException;
use Sparors\Ussd\Exceptions\UniqueIdentifierEmptyException;

class Context
{
    private array $bag;

    public function __construct(
        private string $uid,
        private string $gid,
        private string $input
    ) {
        if (0 === strlen($uid)) {
            throw new UniqueIdentifierEmptyException();
        }

        if (0 === strlen($gid)) {
            throw new GlobaldentifierEmptyException();
        }

        $this->bag = [];
    }

    public static function create(string $uid, string $sid, string $input): static
    {
        return new static($uid, $sid, $input);
    }

    public function with(array $parameters): static
    {
        $this->bag = $parameters;

        return $this;
    }

    public function uid(): string
    {
        return $this->uid;
    }

    public function gid(): string
    {
        return $this->gid;
    }

    public function input(): string
    {
        return $this->input;
    }

    public function get(string $key): mixed
    {
        return $this->bag[$key] ?? null;
    }
}
