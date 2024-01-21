<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Attributes\Terminate;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Menu;
use Sparors\Ussd\Record;

#[Terminate]
class FinishingState implements State
{
    public function render(Record $record): Menu
    {
        [$magic, $pop] = $record->getMany(['magic', 'pop']);

        return Menu::build()->line('Tadaa...')->text($magic)->lineBreak()->text($pop);
    }
}
