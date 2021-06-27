<?php

namespace Sparors\Ussd;

class Menu
{
    const NUMBERING_ALPHABETIC_LOWER = 'alphabetic_lower';
    const NUMBERING_ALPHABETIC_UPPER = 'alphabetic_upper';
    const NUMBERING_EMPTY = 'empty';
    const NUMBERING_NUMERIC = 'numeric';

    const ITEMS_SEPARATOR_NO_LINE_BREAK = "";
    const ITEMS_SEPARATOR_LINE_BREAK = "\n";
    const ITEMS_SEPARATOR_DOUBLE_LINE_BREAK = "\n\n";

    const NUMBERING_SEPARATOR_NO_SPACE = "";
    const NUMBERING_SEPARATOR_SPACE = " ";
    const NUMBERING_SEPARATOR_DOUBLE_SPACE = "  ";
    const NUMBERING_SEPARATOR_DOT = ".";
    const NUMBERING_SEPARATOR_DOT_PLUS_SPACE = ". ";
    const NUMBERING_SEPARATOR_DOT_PLUS_DOUBLE_SPACE = ".  ";
    const NUMBERING_SEPARATOR_BRACKET = ")";
    const NUMBERING_SEPARATOR_BRACKET_PLUS_SPACE = ") ";
    const NUMBERING_SEPARATOR_BRACKET_PLUS_DOUBLE_SPACE = ")  ";

    /** @var string */
    protected $menu;

    public function __construct($menu = '')
    {
        $this->menu = $menu;
    }

    protected function numberingFor(int $index, string $numbering): string
    {
        if ($numbering === self::NUMBERING_ALPHABETIC_LOWER) {
            return range('a','z')[$index];
        }
        if ($numbering === self::NUMBERING_ALPHABETIC_UPPER) {
            return range('A','Z')[$index];
        }
        if ($numbering === self::NUMBERING_NUMERIC) {
            return (string) $index + 1;
        }
        return '';
    }

    protected function isLastPage(
        int $page,
        int $numberPerPage,
        array $items
    ): bool {
        return $page * $numberPerPage >= count($items);
    }


    protected function pageStartIndex(int $page, int $numberPerPage): int
    {
        return $page * $numberPerPage - $numberPerPage;
    }

    protected function pageLimit(int $page, int $numberPerPage, array $items): int
    {
        return (
            $this->isLastPage($page, $numberPerPage, $items)
            ? count($items) - $this->pageStartIndex($page, $numberPerPage)
            : $numberPerPage
        );
    }

    private function listParser(
        array $items,
        int $page,
        int $numberPerPage,
        string $numberingSeparator,
        string $itemsSeparator,
        string $numbering
        ): void {
        $startIndex = $this->pageStartIndex($page, $numberPerPage);
        $limit = $this->pageLimit($page, $numberPerPage, $items);
        for ($i = 0; $i < $limit; $i++) {
            $this->menu .= "{$this->numberingFor($i + $startIndex, $numbering)}{$numberingSeparator}{$items[$i + $startIndex]}";
            if ($i !== $limit - 1) {
                $this->menu .= $itemsSeparator;
            }
        }
    }

    public function lineBreak(int $number = 1): self
    {
        $this->menu .= str_repeat("\n", $number);

        return $this;
    }

    public function line(string $text): self
    {
        $this->menu .= "$text\n";

        return $this;
    }

    public function text(string $text): self
    {
        $this->menu .= $text;

        return $this;
    }

    public function listing(
        array $items,
        string $numberingSeparator = self::NUMBERING_SEPARATOR_DOT,
        string $itemsSeparator = self::ITEMS_SEPARATOR_LINE_BREAK,
        string $numbering = self::NUMBERING_NUMERIC
    ): self {
        $this->listParser(
            $items,
            1,
            count($items),
            $numberingSeparator,
            $itemsSeparator,
            $numbering
        );

        return $this;
    }

    public function paginateListing(
        array $items,
        int $page = 1,
        int $numberPerPage = 5,
        string $numberingSeparator = self::NUMBERING_SEPARATOR_DOT,
        string $itemsSeparator = self::ITEMS_SEPARATOR_LINE_BREAK,
        string $numbering = self::NUMBERING_NUMERIC
    ): self {
        $this->listParser(
            $items,
            $page,
            $numberPerPage,
            $numberingSeparator,
            $itemsSeparator,
            $numbering
        );

        return $this;
    }

    public function toString(): string
    {
        return $this->menu;
    }

    public function __toString()
    {
        return $this->menu;
    }
}
