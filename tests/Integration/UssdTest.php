<?php

namespace Sparors\Ussd\Tests\Integration;

use Exception;
use Sparors\Ussd\Ussd;
use Sparors\Ussd\Context;
use Sparors\Ussd\Tests\TestCase;
use Sparors\Ussd\Tests\Dummy\BeginningState;
use Sparors\Ussd\Tests\Dummy\FinishingState;
use Sparors\Ussd\Tests\Dummy\CogConfigurator;
use PHPUnit\Framework\Attributes\DataProvider;
use Sparors\Ussd\ContinuingMode;
use Sparors\Ussd\Tests\Dummy\ContinuingState;
use Sparors\Ussd\Tests\Dummy\SophisticatedState;

final class UssdTest extends TestCase
{
    public function test_ussd_runs_successfully()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Tadaa...\nabracadabra\nHurray!!!!!",
                'terminating' => true
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );
    }

    public function test_ussd_can_paginate()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n3.Baz\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );
    }

    public function test_ussd_can_detect_end_of_paginate()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n3.Baz\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $res = Ussd::build(
            Context::create('1234', '7890', '#')
        )
        ->useInitialState(BeginningState::class)
        ->run();

        $this->assertTrue($res['terminating']);
    }

    public function test_ussd_can_limit_content()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "beginning..\n#.More",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );
    }

    public function test_ussd_can_detect_end_of_limit_content()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "beginning..\n#.More",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => ".",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(BeginningState::class)
            ->run()
        );

        $res = Ussd::build(
            Context::create('1234', '7890', '#')
        )
        ->useInitialState(BeginningState::class)
        ->run();

        $this->assertTrue($res['terminating']);
    }

    public function test_ussd_can_automatically_continue_from_old_session()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONTINUE, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONTINUE, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Tadaa...\nabracadabra\nHurray!!!!!",
                'terminating' => true
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONTINUE, 3600, ContinuingState::class)
            ->run()
        );
    }

    public function test_ussd_can_automatically_start_from_old_session()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::START, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::START, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::START, 3600, ContinuingState::class)
            ->run()
        );
    }

    public function test_ussd_can_manually_continue_from_old_session()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Wanna continue?\n1.Yes\nAny to start",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Tadaa...\nabracadabra\nHurray!!!!!",
                'terminating' => true
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );
    }

    public function test_ussd_can_manually_start_from_old_session()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Wanna continue?\n1.Yes\nAny to start",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('5656', '7890', '2')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );
    }

    public function test_ussd_can_automatically_continue_from_multiple_old_session()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONTINUE, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONTINUE, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Tadaa...\nabracadabra\nHurray!!!!!",
                'terminating' => true
            ],
            Ussd::build(
                Context::create('6565', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONTINUE, 3600, ContinuingState::class)
            ->run()
        );
    }

    public function test_ussd_can_manually_continue_from_multiple_old_session()
    {
        $this->assertEquals(
            [
                'message' => "In the\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Wanna continue?\n1.Yes\nAny to start",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Pick one...Booooom!\n1.Foo\n2.Bar\n",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('5656', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Wanna continue?\n1.Yes\nAny to start",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('6565', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Tadaa...\nabracadabra\nHurray!!!!!",
                'terminating' => true
            ],
            Ussd::build(
                Context::create('6565', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useContinuingState(ContinuingMode::CONFIRM, 3600, ContinuingState::class)
            ->run()
        );
    }

    public function test_ussd_can_limit_content_and_paginate()
    {
        $this->assertEquals(
            [
                'message' => "In the sophisticated world\n1.The quick brown fox jumps over the lazy dog.\n2.A journey of a thousand miles begins with a single step.\n3.Success\n#.More",
                'terminating' => false,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(SophisticatedState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "is not final, failure is not fatal: It is the courage to continue that counts.\n#.Next",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(SophisticatedState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "In the sophisticated world\n4.In the middle of difficulty lies opportunity.\n5.The only way to do great work is to love what you do.\n6.Believe\n#.More",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(SophisticatedState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "you can and you're halfway there.\n#.Next",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(SophisticatedState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "In the sophisticated world\n7.The future belongs to those who believe in the beauty of their dreams.\n8.Don't watch the clock; do what it does.\n#.More",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(SophisticatedState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "Keep going.\n9.The best way to predict the future is to create it.\n#.Next",
                'terminating' => false
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(SophisticatedState::class)
            ->run()
        );

        $this->assertEquals(
            [
                'message' => "In the sophisticated world\n10.Dream big and dare to fail.\n",
                'terminating' => true
            ],
            Ussd::build(
                Context::create('1234', '7890', '#')
            )
            ->useInitialState(SophisticatedState::class)
            ->run()
        );
    }

    /** @dataProvider configurator_as */
    public function test_it_can_make_use_of_a_configurator($operator, $configurator)
    {
        $this->assertEquals(
            [
                'action' => 'input',
                'message' => "In the\n#.More",
                'operator' => $operator,
            ],
            Ussd::build(
                Context::create('1234', '7890', '2')
            )
            ->useInitialState(BeginningState::class)
            ->useConfigurator($configurator)
            ->run()
        );
    }

    public function test_invalid_initial_state_throws_an_exception()
    {
        $this->expectException(Exception::class);

        Ussd::build(
            Context::create('1234', '7890', '2')
        )
        ->useInitialState(FinishingState::class)
        ->run();
    }

    public function test_invalid_continuing_state_throws_an_exception()
    {
        $this->expectException(Exception::class);

        Ussd::build(
            Context::create('1234', '7890', '2')
        )
        ->useInitialState(BeginningState::class)
        ->useContinuingState(ContinuingMode::CONFIRM, 3600, FinishingState::class)
        ->run();
    }

    public function test_invalid_exception_handler_throws_an_exception()
    {
        $this->expectException(Exception::class);

        Ussd::build(
            Context::create('1234', '7890', '2')
        )
        ->useInitialState(BeginningState::class)
        ->useExceptionHandler(FinishingState::class)
        ->run();
    }

    public function test_invalid_configurator_throws_an_exception()
    {
        $this->expectException(Exception::class);

        Ussd::build(
            Context::create('1234', '7890', '2')
        )
        ->useInitialState(BeginningState::class)
        ->useConfigurator(FinishingState::class)
        ->run();
    }

    public static function configurator_as()
    {
        return [
            ['Default', CogConfigurator::class],
            ['Dynamic', new CogConfigurator('Dynamic')],
        ];
    }
}
