<?php

namespace Sparors\Ussd;

use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class Record
{
    private Repository $repository;

    public function __construct(
        ?string $store,
        private string $uid,
        private string $gid
    ) {
        $this->repository = Cache::store($store);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    public function __unset(string $name): void
    {
        $this->forget($name);
    }

    public function __invoke(array|string $argument): mixed
    {
        if (is_string($argument)) {
            return $this->get($argument);
        }

        if (is_array($argument)) {
            return $this->setMany($argument);
        }
    }

    public function has(string $key, bool $public = false): bool
    {
        return $this->repository->has($this->id($key, $public));
    }

    public function set(
        string $key,
        mixed $value,
        null|DateInterval|DateTimeInterface|int $ttl = null,
        bool $public = false
    ): bool {
        return $this->repository->set($this->id($key, $public), $value, $ttl);
    }

    public function setMany(
        array $values,
        null|DateInterval|DateTimeInterface|int $ttl = null,
        bool $public = false
    ): bool {
        $newValues = [];

        foreach ($values as $key => $value) {
            $newValues[$this->id($key, $public)] = $value;
        }

        return $this->repository->setMultiple($newValues, $ttl);
    }

    public function get(string $key, mixed $default = null, bool $public = false): mixed
    {
        return $this->repository->get($this->id($key, $public), $default);
    }

    public function getMany(array $keys, mixed $default = null, bool $public = false): array
    {
        return array_values(
            (array) $this->repository->getMultiple($this->ids($keys, $public), $default)
        );
    }

    public function increment(string $key, mixed $value = 1, bool $public = false): bool|int
    {
        return $this->repository->increment($this->id($key, $public), $value);
    }

    public function decrement(string $key, mixed $value = 1, bool $public = false): bool|int
    {
        return $this->repository->decrement($this->id($key, $public), $value);
    }

    public function forget(string $key, bool $public = false): bool
    {
        return $this->repository->delete($this->id($key, $public));
    }

    public function forgetMany(array $keys, bool $public = false): bool
    {
        return $this->repository->deleteMultiple($this->ids($keys, $public));
    }

    private function id(string $key, bool $public): string
    {
        return 'ussd:'.($public ? $this->gid : $this->uid).":{$key}";
    }

    private function ids(array $keys, bool $public): array
    {
        return array_map(fn ($key) => $this->id($key, $public), $keys);
    }
}
