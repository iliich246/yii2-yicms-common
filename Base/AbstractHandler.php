<?php

namespace Iliich246\YicmsCommon\Base;

/**
 * Class AbstractHandler
 *
 * All handlers must inherit this class. This class implements methods for work with handler buffer
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractHandler
{
    /** @var object aggregator for this handler */
    protected $aggregator;
    /** @var array buffer for caching data of handler */
    private $buffer = [];

    /**
     * Retrieves a value from cache with a specified key.
     * @param string $key a key identifying the cached value. Key must be a string.
     * @return bool|object the object stored in the cache, false if value not in the cache.
     */
    protected function getFromCache($key)
    {
        if (isset($this->buffer[$key])) return $this->buffer[$key];
        return false;
    }

    /**
     * Stores a value identified by a key into cache.
     * If the cache already contains such a key, the existing value
     * will be replaced with the new one.
     *
     * @param string $key a key identifying the cached value. Key must be a string.
     * @param object $data the object to be cached
     * @return void
     */
    protected function setToCache($key, $data)
    {
        $this->buffer[$key] = $data;
    }

    /**
     * Checks whether a specified key exists in the cache.
     * @param string $key a key identifying the cached value. Key must be a string.
     * @return bool
     */
    protected function existsInCache($key)
    {
        if (isset($this->buffer[$key])) return true;
        return false;
    }

    /**
     * Method combines both [[set()]] and [[get()]] methods to retrieve value identified by a $key,
     * or to store the result of $callable execution if there is no cache available for the $key.
     *
     * @param string $key key identifying the cached value. Key must be a string.
     * @param callable|\Closure $callable the callable or closure that will be used to generate a value to be cached.
     * In case $callable returns `false`, the value will not be cached.
     * @return bool|object result of $callable execution or the object stored in the cache,
     * false if value not in the cache or callable return false.
     */
    protected function getOrSet($key, $callable)
    {
        if (($value = $this->getFromCache($key)) !== false) {
            return $value;
        }

        $value = call_user_func($callable, $this);

        if ($value) $this->setToCache($key, $value);

        return $value;
    }
}
