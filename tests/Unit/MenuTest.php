<?php

namespace Sparors\Ussd\Tests\Unit;

use Sparors\Ussd\Menu;
use PHPUnit\Framework\TestCase;

final class MenuTest extends TestCase
{
    public function test_menu_can_be_used_as_string()
    {
        $menu = new Menu('Hello Ussd');
        $this->assertEquals('Hello Ussd', $menu);
    }

    public function test_menu_can_be_converted_to_string_explicitly()
    {
        $menu = new Menu('Hello Ussd');
        $this->assertEquals('Hello Ussd', (string) $menu);
    }

    public function test_menu_can_have_line_break()
    {
        $this->assertEquals("\n", Menu::build()->lineBreak());
    }

    public function test_menu_can_have_double_line_break()
    {
        $this->assertEquals("\n\n", Menu::build()->lineBreak(2));
    }

    public function test_menu_can_have_text_with_line_break()
    {
        $this->assertEquals("Hello Ussd\n", Menu::build()->line("Hello Ussd"));
    }

    public function test_menu_can_have_text_with_no_line_break()
    {
        $this->assertEquals("Hello Ussd", Menu::build()->text("Hello Ussd"));
    }

    public function test_menu_can_parse_a_list_to_string()
    {
        $this->assertEquals(
            "1.New Gen\n2.Old Gen\n",
            Menu::build()->listing(['New Gen', 'Old Gen'])
        );
    }

    public function test_menu_can_paginate_and_parse_a_list_to_string()
    {
        $this->assertEquals(
            "3.Extra\n",
            Menu::build()->listing(['New Gen', 'Old Gen', 'Extra'], page: 2, perPage: 2)
        );
    }

    public function test_menu_can_parse_a_list_to_string_with_custom_numbering()
    {
        $this->assertEquals(
            "a.New Gen\nb.Old Gen\n",
            Menu::build()->listing(
                ['New Gen', 'Old Gen'],
                fn (int $index) => range('a', 'z')[$index]
            )
        );
    }

    public function test_menu_method_can_be_chained()
    {
        $this->assertEquals(
            "Hello Ussd\n1.Ok\n2.Fine\n\nBye",
            Menu::build()->line('Hello Ussd')->listing(['Ok', 'Fine'])->lineBreak()->text('Bye')
        );
    }

    public function test_menu_can_be_appended()
    {
        $this->assertEquals(
            "First Here\nAppended",
            Menu::build()->line("First Here")->append(fn (Menu $menu) => $menu->text("Appended"))
        );
    }

    public function test_menu_can_be_prepened()
    {
        $this->assertEquals(
            "Prepended\nFirst Here",
            Menu::build()->text("First Here")->prepend(fn (Menu $menu) => $menu->line("Prepended"))
        );
    }

    /** @dataProvider data_true_or_false */
    public function test_menu_can_conditionally_adjusted_with_when($expected, $condition)
    {
        $this->assertEquals(
            "That...\n{$expected}",
            Menu::build()
                ->line("That...")
                ->when(
                    $condition,
                    fn (Menu $menu) => $menu->text("So True"),
                    fn (Menu $menu) => $menu->text('Whatever')
                )
        );
    }

    /** @dataProvider data_true_or_false */
    public function test_menu_can_conditionally_adjusted_with_unless($expected, $condition)
    {
        $this->assertEquals(
            "That...\n{$expected}",
            Menu::build()
                ->line("That...")
                ->unless(
                    $condition,
                    fn (Menu $menu) => $menu->text('Whatever'),
                    fn (Menu $menu) => $menu->text("So True")
                )
        );
    }

    public static function data_true_or_false()
    {
        return [
            ['So True', true],
            ['Whatever', false],
        ];
    }
}
