<?php

namespace Sparors\Ussd\Tests\Integration;

use Sparors\Ussd\ContinuingMode;
use Sparors\Ussd\Tests\Dummy\BeginningState;
use Sparors\Ussd\Tests\Dummy\ContinuingState;
use Sparors\Ussd\Tests\TestCase;
use Sparors\Ussd\Ussd;

final class AssestionTest extends TestCase
{
    public function test_ussd_assestions_work_successfully()
    {
        Ussd::test(BeginningState::class, ContinuingMode::CONFIRM, null, ContinuingState::class)
            ->additional(['foo' => 'bar'])
            ->actingAs('isaac')
            ->start()
            ->assertSee('In the')
            ->assertContextHas('foo')
            ->assertContextHas('foo', 'bar')
            ->assertContextHas('foo', fn ($value) => $value === 'bar')
            ->assertContextMissing('baz')
            ->assertRecordMissing('wow')
            ->input('#')
            ->assertSee('beginning..')
            ->input('1')
            ->assertSee('Pick one...')
            ->assertSee('Foo')
            ->assertRecordHas('wow')
            ->input('#')
            ->assertSee('Pick one...')
            ->assertSee('Baz')
            ->actingAs('benjamin')
            ->assertSee('In the')
            ->input('1')
            ->assertSee('Pick one...')
            ->actingAs('isaac')
            ->input('1')
            ->assertSee('Tadaa...')
            ->actingAs('benjamin')
            ->input('1')
            ->assertRecordHas('wow')
            ->assertSee('Tadaa...')
            ->timeout()
            ->assertSee('In the')
            ->input('1')
            ->assertSee('Pick one...')
            ->actingAs('isaac');
    }

    public function test_ussd_assestion_can_wait_for_some_time_to_pass()
    {
        Ussd::test(BeginningState::class, ContinuingMode::CONFIRM, 5, ContinuingState::class)
            ->actingAs('isaac')
            ->start()
            ->input('1')
            ->timeout(6)
            ->assertSee('In the')
            ->timeout(3)
            ->assertSee('Wanna continue')
            ->input('1')
            ->input('1')
            ->assertSee('Tadaa...')
            ->timeout()
            ->assertSee('In the');
    }
}
