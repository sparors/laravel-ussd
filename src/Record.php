<?php

namespace Sparors\Ussd;

use Illuminate\Contracts\Cache\Repository as Cache;

class Record
{
    /** @var Cache */
    protected $cache;

    /** @var string */
    protected $id;

    public function __construct(Cache $cache, $id)
    {
        $this->cache = $cache;
        $this->id = $id;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getKey($key)
    {
        return "$this->id.$key";
    }

    /**
     * @param int $ttl
     * @return \DateTimeInterface|\DateInterval|int|null
     */
    private function getTtl($ttl)
    {
        return $ttl ?? config('ussd.cache_ttl');
    }

    /**
     * @param array $keys
     * @return array
     */
    private function getKeys($keys)
    {
        return array_map(
            function ($key) { return $this->getKey($key); },
            $keys
        );
    }

    /**
     * @param array $values
     * @return array
     */
    private function getValues($values)
    {
        $newValues = array();
        foreach($values as $key => $value) {
            $newValues[$this->getKey($key)] = $value;
        }

        return $newValues;
    }

     /**
     * Determine if an item exists in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->cache->has($this->getKey($key));
    }

    /**
     * Store an item in the record.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->cache->set($this->getKey($key), $value, $ttl);
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        return $this->cache->setMultiple($this->getValues($values), $ttl);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->cache->get($this->getKey($key), $default);
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @param string $default
     * @return array
     */
    public function getMultiple($keys, $default = null)
    {
        return array_values(
            (array)$this->cache->getMultiple($this->getKeys($keys), $default)
        );
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->cache->delete($this->getKey($key));
    }

    /**
     * Remove an item from the cache
     *
     * @param  string  $key
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return $this->cache->deleteMultiple($this->getKeys($keys));
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->clear();
    }

    public function __set($name, $value)
    {
        $this->set($name, $value, config('ussd.cache_ttl'));
    }

    public function __get($name)
    {
        return $this->get($name, config('ussd.cache_default'));
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __unset($name)
    {
        return $this->delete($name);
    }

    public function __invoke($argument)
    {
        if (is_string($argument)) {
            return $this->get($argument, config('ussd.cache_default'));
        } else if (is_array($argument)) {
            $this->setMultiple($argument, config('ussd.cache_ttl'));
        }
    }
}
