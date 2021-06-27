<?php

namespace Sparors\Ussd\Tests;

use Orchestra\Testbench\TestCase as Testbench;
use Sparors\Ussd\UssdServiceProvider;

abstract class TestCase extends Testbench
{
    /**
     * Tell Testbench to use this package.
     *
     * @param $app
     *
     * @return array
     */
    public function getPackageProviders($app)
    {
        return [UssdServiceProvider::class];
    }
}
