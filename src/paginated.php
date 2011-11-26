<?php

class PaginatedCollection extends LimitIterator implements ArrayAccess, Countable
{
    public function __construct($collection, $page = 1, $items_per_page = 10)
    {
        if(is_array($collection)) $collection = new ArrayIterator($collection);

        $this->collection = $collection;
        $this->items_per_page = $items_per_page;

        $this->setPage($page);

        $offset = ($page-1)*$this->items_per_page;
        $limit = $this->items_per_page;

        $iterator = new LimitIterator($this->collection, $offset, $limit);
        parent::__construct($iterator);
    }

    private function setPage($page)
    {
        if(($pages = $this->getPagesTotal()) >= 0) {
            $page = min($page, $pages);
        }

        $this->page = max($page, 1);
    }

    public function loadCurrentPage()
    {
        /* HACK: Ensure the current page items are loaded */
        if($this->collection instanceof Delayed) {

            $offset = ($this->page-1)*$this->items_per_page;
            $limit = $this->items_per_page;
            $this->collection->fetch($offset, $limit);
        }
    }

    public function getInnerIterator()
    {
        return $this->iterator;
    }

    public function getTotal()
    {
        return count($this->collection);
    }

    public function getPagesTotal()
    {
        if(($total = $this->getTotal()) >= 0) {
            return ceil($total/$this->items_per_page);
        }
        else return -1;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function offsetSet($index, $value)
    {
        $index += ($this->page-1)*$this->items_per_page;
        $this->collection[$index] = $value;
    }

    public function offsetUnset($index)
    {
        $index += ($this->page-1)*$this->items_per_page;
        unset($this->collection[$index]);
    }

    public function offsetGet($index)
    {
        $index += ($this->page-1)*$this->items_per_page;
        return isset($this->collection[$index]) ? $this->collection[$index] : null;
    }

    public function offsetExists($index)
    {
        $index += ($this->page-1)*$this->items_per_page;
        return isset($this->collection[$index]);
    }

    public function count()
    {
        if(($total = $this->getTotal()) < 0) {
            /* Total unknown */
            return $this->items_per_page;
        } else {
            $offset = ($this->page-1)*$this->items_per_page;
            return min($this->getTotal()-$offset, $this->items_per_page);
        }
    }
}

