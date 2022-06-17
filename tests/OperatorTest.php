<?php

namespace Sparors\Ussd\Tests;

use Illuminate\Support\Facades\Log;
use Sparors\Ussd\Machine;
use Sparors\Ussd\Operators\DefaultOperator;
use Sparors\Ussd\Facades\Ussd as UssdFacade;
use Sparors\Ussd\Tests\Operators\NotInstanceOfOperator;
use Sparors\Ussd\Tests\Operators\TestOperator;

class OperatorTest extends TestCase
{
    public function test_it_does_not_use_an_operator_when_none_is_set_in_the_config()
    {
        config()->set('ussd.operator', null);
        $this
            ->spy(DefaultOperator::class)
            ->shouldNotHaveBeenCalled();

        UssdFacade::machine();
    }

    public function test_it_uses_an_operator_when_one_is_set_in_the_config()
    {
        config()->set('ussd.operator', TestOperator::class);
        $machine = new Machine();

        $this
            ->spy(TestOperator::class)
            ->shouldReceive('decorate')
            ->once()
            ->andReturn($machine);

        UssdFacade::machine();
    }

    public function test_it_does_not_use_an_invalid_operator()
    {
        /**
         * @todo: Throw an exception when the operator is not an instance of OperatorContract
         */

        config()->set('ussd.operator', NotInstanceOfOperator::class);

        $this
            ->spy(NotInstanceOfOperator::class)
            ->shouldNotHaveBeenCalled();

        UssdFacade::machine();
    }

    public function test_it_does_not_use_a_non_existent_operator()
    {
        config()->set('ussd.operator', 'Namespace\\NonExistentOperator');

        $this
            ->spy(NotInstanceOfOperator::class)
            ->shouldNotHaveBeenCalled();

        UssdFacade::machine();
    }

    /**
     * @todo: Throw an exception when the operator is not an instance of OperatorContract
     */
    public function test_it_logs_a_warning_for_non_existent_operator()
    {
        config()->set('ussd.operator', 'Namespace\\NonExistentOperator');

        Log::shouldReceive('warning')
            ->once()
            ->with('Target class [Namespace\\NonExistentOperator] does not exist.');

        UssdFacade::machine();
    }
}