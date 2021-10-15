<?php

namespace Sparors\Ussd\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Sparors\Ussd\Record record(string $id, string $store = null)
 * @method static \Sparors\Ussd\Menu menu(string $menu)
 * @method static \Sparors\Ussd\Machine machine()
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
