<?php

namespace Sparors\Ussd;

use Illuminate\Support\Facades\Log;
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
        $configuredOperator = config('ussd.operator');
        $machine = new Machine();

        try {
            $operator = $this->app->make($configuredOperator);
        }catch (\Exception $exception){
            Log::warning($exception->getMessage());
            return $machine;
        }

        if (!$operator instanceof OperatorContract) {
            return $machine;
        }

        return $operator->decorate($machine);
    }
}
