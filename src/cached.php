<?php

abstract class CachedCollection implements ArrayAccess, Iterator, Countable, Delayed
{
    protected $collection;
    protected $cache_id;

    public function __construct(Delayed $collection, $cache_id)
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

class SessionCachedCollection extends CachedCollection
{
    protected $cache_ttl;

    public function __construct(Delayed $collection, $cache_id, $session_id, $cache_ttl = 60)
    {
        session_id($session_id);
        if(!isset($_SESSION)) {
            session_start();
        }
        $this->cache_ttl = $cache_ttl;
        parent::__construct($collection, $cache_id);
    }

    private function cacheSpaceCreate()
    {
        if(!isset($_SESSION['cache'])) $_SESSION['cache'] = array();
        if(!isset($_SESSION['cache'][$this->cache_id])
             || !isset($_SESSION['cache'][$this->cache_id]['time'])
             || !isset($_SESSION['cache'][$this->cache_id]['items']))
                $_SESSION['cache'][$this->cache_id] = array('time'=>time(), 'items'=>array());
    }

    private function cacheSpaceGetValid()
    {
        $this->cacheSpaceCreate();

        if($_SESSION['cache'][$this->cache_id]['time']+$this->cache_ttl < time()) {
            unset($_SESSION['cache'][$this->cache_id]);
            $this->cacheSpaceCreate();
        }
    }

    protected function cacheGet(&$result, $offset, $limit = 1)
    {
        $this->cacheSpaceGetValid();
        return array_keys_range_exist($_SESSION['cache'][$this->cache_id]['items'], $offset, $limit, $result);
    }

    protected function cacheSet($result)
    {
        $this->cacheSpaceGetValid();
        $_SESSION['cache'][$this->cache_id]['items'] += $result;
    }
}

