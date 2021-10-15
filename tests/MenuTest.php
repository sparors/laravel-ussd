<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Menu;
use PHPUnit\Framework\TestCase;

class MenuTest extends TestCase
{
    public function test_it_can_be_used_as_string()
    {
        $menu = new Menu('Hello Ussd');
        $this->assertEquals('Hello Ussd', $menu);
    }

    public function test_it_can_be_converted_to_string_explicitly()
    {
        $menu = new Menu('Hello Ussd');
        $this->assertEquals('Hello Ussd', $menu->toString());
    }

    public function test_it_can_have_line_break()
    {
        $menu = new Menu();
        $this->assertEquals("\n", $menu->lineBreak());
    }

    public function test_it_can_have_double_line_break()
    {
        $menu = new Menu();
        $this->assertEquals("\n\n", $menu->lineBreak(2));
    }

    public function test_it_can_have_text_with_line_break()
    {
        $menu = new Menu();
        $this->assertEquals("Hello Ussd\n", $menu->line("Hello Ussd"));
    }

    public function test_it_can_have_text_with_no_line_break()
    {
        $menu = new Menu();
        $this->assertEquals("Hello Ussd", $menu->text("Hello Ussd"));
    }

    public function test_it_can_parse_a_list_to_string()
    {
        $menu = new Menu();
        $this->assertEquals(
            "1.New Gen\n2.Old Gen",
            $menu->listing(['New Gen', 'Old Gen'])
        );
    }

    public function test_it_can_paginate_and_parse_a_list_to_string()
    {
        $menu = new Menu();
        $this->assertEquals(
            "3.Extra",
            $menu->paginateListing(['New Gen', 'Old Gen', 'Extra'], 2, 2)
        );
    }

    public function test_it_can_parse_a_list_to_string_with_alphabets_lower_for_numbering()
    {
        $menu = new Menu();
        $this->assertEquals(
            "a.New Gen\nb.Old Gen",
            $menu->listing(
                ['New Gen', 'Old Gen'],
                Menu::NUMBERING_SEPARATOR_DOT,
                Menu::ITEMS_SEPARATOR_LINE_BREAK,
                Menu::NUMBERING_ALPHABETIC_LOWER
            )
        );
    }

    public function test_it_can_parse_a_list_to_string_with_alphabets_upper_for_numbering()
    {
        $menu = new Menu();
        $this->assertEquals(
            "A.New Gen\nB.Old Gen",
            $menu->listing(
                ['New Gen', 'Old Gen'],
                Menu::NUMBERING_SEPARATOR_DOT,
                Menu::ITEMS_SEPARATOR_LINE_BREAK,
                Menu::NUMBERING_ALPHABETIC_UPPER
            )
        );
    }

    public function test_it_can_parse_a_list_to_string_with_empty_string_for_numbering()
    {
        $menu = new Menu();
        $this->assertEquals(
            ".New Gen\n.Old Gen",
            $menu->listing(
                ['New Gen', 'Old Gen'],
                Menu::NUMBERING_SEPARATOR_DOT,
                Menu::ITEMS_SEPARATOR_LINE_BREAK,
                Menu::NUMBERING_EMPTY
            )
        );
    }

    public function test_method_can_be_chained()
    {
        $menu = new Menu();
        $this->assertEquals(
            "Hello Ussd\n1.Ok\n2.Fine\nBye",
            $menu->line('Hello Ussd')->listing(['Ok', 'Fine'])->lineBreak()->text('Bye')
        );
    }
}
