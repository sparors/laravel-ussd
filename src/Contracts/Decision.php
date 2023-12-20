<?php

namespace Sparors\Ussd\Contracts;


interface Decision
{
    public function decide(string $actual): bool;
}
