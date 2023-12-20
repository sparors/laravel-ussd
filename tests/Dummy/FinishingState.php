<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Menu;
use Sparors\Ussd\Record;

class FinishingState implements State
{
    public function render(Record $record): Menu
    {
        [$magic, $pop] = $record->getMany(['magic', 'pop']);

        return Menu::build()->line('Tadaa...')->text($magic)->lineBreak()->text($pop);
    }
}
