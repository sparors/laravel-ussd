<?php

namespace Sparors\Ussd\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Sparors\Ussd\Record;

trait WithPagination
{
    private static $currentPage;

    private function lastPage(): int
    {
        return ceil(count($this->getItems()) / $this->perPage());
    }

    public function currentPage(): int
    {
        if (isset(self::$currentPage)) {
            return self::$currentPage;
        }

        /** @var Record */ $record =  App::make(Record::class);
        $pageId = Str::of(get_called_class())->replace('\\', '')->snake()->append('_page')->value();
        $page = $record->get($pageId, 1);

        self::$currentPage = $page;

        return $page;
    }

    public function isFirstPage(): int
    {
        return 1 === $this->currentPage();
    }

    public function isLastPage(): int
    {
        return $this->currentPage() === $this->lastPage();
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage() < $this->lastPage();
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage() > 1;
    }

    abstract public function getItems(): array;

    abstract public function perPage(): int;

    public function __destruct()
    {
        self::$currentPage = null;
    }
}
