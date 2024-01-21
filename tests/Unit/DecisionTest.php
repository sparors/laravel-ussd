<?php

namespace Sparors\Ussd\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sparors\Ussd\Decisions\Between;

final class DecisionTest extends TestCase
{
    /** @dataProvider data_between_decision*/
    public function test_between_decision_works($value, $bool)
    {
        $decision = new Between(5, 10);

        $this->assertEquals($decision->decide($value), $bool);
    }

    public static function data_between_decision()
    {
        return [
            [5, true],
            [10, true],
            [7, true],
            [1, false],
            [11, false],
        ];
    }
}
