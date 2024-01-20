<?php

namespace Sparors\Ussd\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Sparors\Ussd\Record;

trait WithPagination
{
    private static $cache = [];

    private function lastPage(): int
    {
        return ceil(count($this->getItems()) / $this->perPage());
    }

    public function currentPage(): int
    {
        $name = get_called_class();

        if (isset(static::$cache[$name])) {
            return static::$cache[$name];
        }

        /** @var Record */ $record =  App::make(Record::class);
        $pageId = Str::of($name)->replace('\\', '')->snake()->append('_page')->value();
        $page = $record->get($pageId, 1);

        return static::$cache[$name] = $page;
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
        static::$cache = [];
    }
}
