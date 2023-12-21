<?php

namespace Sparors\Ussd\Tests\Integration;

use Sparors\Ussd\ContinuingMode;
use Sparors\Ussd\Tests\Dummy\BeginningState;
use Sparors\Ussd\Tests\Dummy\ContinuingState;
use Sparors\Ussd\Tests\TestCase;
use Sparors\Ussd\Ussd;

final class AssestionTest extends TestCase
{
    public function test_assestion_runs_successfully()
    {
        Ussd::test(BeginningState::class, ContinuingMode::CONFIRM, null, ContinuingState::class)
            ->additional(['foo' => 'bar'])
            ->actingAs('isaac')
            ->start()
            ->assertSee('In the beginning...')
            ->assertContextHas('foo')
            ->assertContextHas('foo', 'bar')
            ->assertContextHas('foo', fn ($value) => $value === 'bar')
            ->assertContextMissing('baz')
            ->assertRecordMissing('wow')
            ->input('1')
            ->assertSee('Pick one...')
            ->assertSee('Foo')
            ->assertRecordHas('wow')
            ->input('#')
            ->assertSee('Pick one...')
            ->assertSee('Baz')
            ->actingAs('benjamin')
            ->assertSee('In the beginning...')
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
            ->assertSee('Wanna continue?')
            ->input('9')
            ->assertSee('In the beginning...')
            ->input('1')
            ->assertSee('Pick one...')
            ->actingAs('isaac');
    }
}
