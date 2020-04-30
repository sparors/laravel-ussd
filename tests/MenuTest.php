<?php

namespace Sparors\Ussd\Tests;

use Orchestra\Testbench\TestCase;
use Sparors\Ussd\Menu;

class MenuTest extends TestCase
{
    public function testCanBeUsedAsString()
    {
        $menu = new Menu('Hello Ussd');
        $this->assertEquals('Hello Ussd', $menu);
    }

    public function testCanBeConvertedToStringExplicitly()
    {
        $menu = new Menu('Hello Ussd');
        $this->assertEquals('Hello Ussd', $menu->toString());
    }

    public function testCanHaveLineBreak()
    {
        $menu = new Menu;
        $this->assertEquals("\n", $menu->lineBreak());
    }

    public function testCanHaveDoubleLineBreak()
    {
        $menu = new Menu;
        $this->assertEquals("\n\n", $menu->lineBreak(2));
    }

    public function testCanHaveTextWithLineBreak()
    {
        $menu = new Menu;
        $this->assertEquals("Hello Ussd\n", $menu->line("Hello Ussd"));
    }

    public function testCanHaveTextWithNoLineBreak()
    {
        $menu = new Menu;
        $this->assertEquals("Hello Ussd", $menu->text("Hello Ussd"));
    }

    public function testCanParseAListToString()
    {
        $menu = new Menu;
        $this->assertEquals("1.New Gen\n2.Old Gen",
            $menu->listing(['New Gen', 'Old Gen']));
    }

    public function testCanPaginateAndParseAListToString()
    {
        $menu = new Menu;
        $this->assertEquals("3.Extra",
            $menu->paginateListing(['New Gen', 'Old Gen', 'Extra'], 2, 2));
    }

    public function testMethodCanBeChained()
    {
        $menu = new Menu;
        $this->assertEquals("Hello Ussd\n1.Ok\n2.Fine\nBye",
            $menu->line('Hello Ussd')->listing(['Ok', 'Fine'])->lineBreak()->text('Bye'));
    }
}
