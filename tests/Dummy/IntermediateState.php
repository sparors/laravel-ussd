<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Attributes\Paginate;
use Sparors\Ussd\Attributes\Transition;
use Sparors\Ussd\Menu;
use Sparors\Ussd\Contracts\State;
use Sparors\Ussd\Decisions\Equal;
use Sparors\Ussd\Record;
use Sparors\Ussd\Traits\WithPagination;

#[Paginate([Equal::class, '#'], [Equal::class, '0'])]
#[Transition(GrandAction::class, [Equal::class, 1], DoTheThing::class)]
class IntermediateState implements State
{
    use WithPagination;

    public function render(Record $record): Menu
    {
        return Menu::build()
            ->text('Pick one...')
            ->when($record->has('wow'), function (Menu $menu) {
                $menu->line('Booooom!');
            })
            ->listing($this->getItems(), page: $this->currentPage(), perPage: $this->perPage());
    }

    public function getItems(): array
    {
        return ['Foo', 'Bar', 'Baz'];
    }

    public function perPage(): int
    {
        return 2;
    }
}
