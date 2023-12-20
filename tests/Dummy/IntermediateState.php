<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Attributes\Transition;
use Sparors\Ussd\Menu;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Decisions\Equal;
use Sparors\Ussd\Record;

#[Transition(GrandAction::class, [Equal::class, 1], DoTheThing::class)]
class IntermediateState implements State
{
    public function render(Record $record): Menu
    {
        return Menu::build()
            ->text('Now see the magic...')
            ->when($record->has('wow'), function (Menu $menu) {
                $menu->lineBreak()->text('Booooom!');
            });
    }
}
