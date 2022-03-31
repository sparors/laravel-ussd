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
    protected function getKey($key)
    {
        return "ussd_$this->id.$key";
    }

    /**
     * @param int $ttl
     * @return \DateTimeInterface|\DateInterval|int|null
     */
    protected function getTtl($ttl)
    {
        return $ttl ?? config('ussd.cache_ttl');
    }

    /**
     * @param string $default
     * @return mixed
     */
    protected function getDefault($default)
    {
        return $default ?? config('ussd.cache_default');
    }

    /**
     * @param array $keys
     * @return array
     */
    protected function getKeys($keys)
    {
        return array_map(
            function ($key) {
                return $this->getKey($key);
            },
            $keys
        );
    }

    /**
     * @param array $values
     * @return array
     */
    protected function getValues($values)
    {
        $newValues = [];
        foreach ($values as $key => $value) {
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
        return $this->cache->set($this->getKey($key), $value, $this->getTtl($ttl));
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
        return $this->cache->setMultiple($this->getValues($values), $this->getTtl($ttl));
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
        return $this->cache->get($this->getKey($key), $this->getDefault($default));
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
            (array) $this->cache->getMultiple($this->getKeys($keys), $this->getDefault($default))
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
     * Increment the value of an item in the cache.
     *
     * @since v2.0.0
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->cache->increment($this->getKey($key), $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @since v2.0.0
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->cache->decrement($this->getKey($key), $value);
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
        }

        if (is_array($argument)) {
            $this->setMultiple($argument, config('ussd.cache_ttl'));
        }
    }
}
