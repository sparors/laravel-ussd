<?php

namespace Sparors\Ussd\Tests\Dummy;

use Sparors\Ussd\Record;

class DoTheThing
{
    public function __invoke(Record $record)
    {
        $record->set('pop', 'Hurray!!!!!');
    }
}
