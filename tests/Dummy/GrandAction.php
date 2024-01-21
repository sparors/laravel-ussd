<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Contracts\Action;
use Sparors\Ussd\Record;

class GrandAction implements Action
{
    public function execute(Record $record): string
    {
        $record->set('magic', 'abracadabra');

        return FinishingState::class;
    }
}
