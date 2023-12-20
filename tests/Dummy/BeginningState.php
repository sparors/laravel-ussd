<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Attributes\Transition;
use Sparors\Ussd\Context;
use Sparors\Ussd\Contracts\InitialState;
use Sparors\Ussd\Decisions\Equal;
use Sparors\Ussd\Menu;
use Sparors\Ussd\Record;

#[Transition(PetitAction::class, [Equal::class, 1], [self::class, 'callback'])]
class BeginningState implements InitialState
{
    public function render(): Menu
    {
        return Menu::build()->text('In the beginning...');
    }

    public function callback(Record $record, Context $context)
    {
        $record->set('wow', $context->input());
    }
}
