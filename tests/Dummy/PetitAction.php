<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Contracts\Action;

class PetitAction implements Action
{
    public function execute(): string
    {
        return IntermediateState::class;
    }
}
