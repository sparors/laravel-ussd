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
     * @return Machine
     */
    public function machine()
    {
        return new Machine();
    }
}