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
    public function getRecord(string $id, string $store = null)
    {
        return new Record(Cache::store($store), $id);
    }
}