<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Contracts\ContinueState;
use Sparors\Ussd\Menu;

class ContinuingState implements ContinueState
{
    public function render(): Menu
    {
        return Menu::build()
            ->line('Wanna continue?')
            ->listing(['Yes'])
            ->text('Any to start');
    }

    public function confirm(): string
    {
        return '1';
    }
}
