<?php

namespace Devbase\Collection;

abstract class CachedCollection implements \ArrayAccess, \Iterator, \Countable, LazyCollection
{
    protected $collection;
    protected $cache_id;

    public function __construct(LazyCollection $collection, $cache_id)
    {
        $this->collection = $collection;
        $this->cache_id = $cache_id;
    }

    public function fetch($offset, $limit = 1)
    {
        if(!$this->cacheGet($result, $offset, $limit)) {
            $result = $this->collection->fetch($offset, $limit);
            if($result) $this->cacheSet($result);
        }
        return $result;
    }

    public function offsetSet($index, $value)
    {
         trigger_error("CachedCollection is read-only.");
    }

    public function offsetUnset($index)
    {
         trigger_error("CachedCollection is read-only.");
    }

    public function offsetGet($index)
    {
        $result = $this->fetch($index);
        return isset($result[$index]) ? $result[$index] : null;
    }

    public function offsetExists($index)
    {
        $result = $this->fetch($index);
        return isset($result[$index]);
    }

    public function rewind()
    {
        $this->collection->rewind();
    }

    public function current()
    {
        return $this->offsetGet($this->collection->key());
    }

    public function key()
    {
        return $this->collection->key();
    }

    public function next()
    {
        $this->collection->next();
    }

    public function valid()
    {
        return $this->collection->valid();
    }

    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Get the chunk from cache
     */
    abstract protected function cacheGet(&$result, $offset, $limit);

    /**
     * Get the chunk from the collection and save it into the cache
     */
    abstract protected function cacheSet($result);
}

