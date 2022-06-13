<?php

namespace Sparors\Ussd;

use Sparors\Ussd\Contracts\OperatorContract;
use Sparors\Ussd\Operators\DefaultOperator;

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
        $configuredOperator = config('ussd.operator',DefaultOperator::class);
        $operator = new $configuredOperator();
        return $operator->decorate(new Machine());
    }
}
