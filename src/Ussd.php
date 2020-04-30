<?php

namespace Sparors\Ussd;

use Illuminate\Support\Facades\Cache;

class Ussd
{
    /**
     * An instance on Application
     * 
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param string $id;
     * @param string $store
     * @return Record
     */
    public function record(string $id, string $store = null)
    {
        return new Record(Cache::store($store), $id);
    }

    public function menu(string $menu = '')
    {
        return new Menu($menu);
    }

    public function machine()
    {
        
    }
}