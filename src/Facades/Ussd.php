<?php

namespace Sparors\Ussd\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Sparors\Ussd\Record getRecord(string $id, string $store = null)
 * 
 * @see \Sparors\Ussd\Ussd
 */
class Ussd extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ussd';
    }
}
