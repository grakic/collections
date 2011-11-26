<?php

class DelayedCollection implements ArrayAccess, Iterator, Countable, Delayed
{
    protected $collection = array();
    protected $position = 0;
    protected $callable;
    protected $total_count;     /* this is real total count */
    protected $max_count;       /* total count is <= max_count */
    protected $chunk_size;

    public function __construct($callable, $chunk_size = 1, $total_count = null)
    {
        $this->max_count = $total_count;
        $this->total_count = $total_count;
        $this->chunk_size = $chunk_size;
        $this->callable = $callable;
    }

    public function fetch($offset, $limit = 1)
    {
//echo "Delayed fetch($offset, $limit)\n";
//debug_print_backtrace();
        if($this->validPosition($offset) && !array_keys_range_exist($this->collection, $offset, $limit, $result)) {

            $lambda = $this->callable;
            /* Always try to get at least two, so iteration can stop on time */
            $chunk = max($limit+1, $this->chunk_size);
            if(isset($this->max_count)) $chunk = min($chunk, $this->max_count);

//echo "Lambda fetch($offset, $chunk)\n";
//debug_print_backtrace();
            $values = $lambda($offset, $chunk);

            /* If the next is not set, we got the end */
            $count = count($values);

            if($count < $chunk) {
                $this->max_count = $offset+$count;                  // aggresive limit
                if($count) $this->total_count = $offset+$count;     // we got something, set as real limit
            } else {
                unset($values[$count-1]);
                $count--;
            }

            if($count > 0) {
                /* Values are returned from zero index */
                $keys = range($offset, $offset+$count-1);
                $result = array_combine($keys, $values);
                $this->collection += $result;
            } else {
                /* Empty */
                $retsult = $values;
            }
        }

        return $result;
    }

    public function offsetSet($index, $value)
    {
         $this->collection[$index] = $value;
    }

    public function offsetUnset($index)
    {
        unset($this->collection[$index]);
    }

    public function offsetGet($index)
    {
        if(!$this->validPosition($index)) return null;

        $result = $this->fetch($index);
        return isset($result[$index]) ? $result[$index] : null;
    }

    public function offsetExists($index)
    {
        if(!$this->validPosition($index)) return false;

        $result = $this->fetch($index);
        return isset($result[$index]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->offsetGet($this->position);
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

    public function valid()
    {
        return $this->validPosition($this->position);
    }

    protected function validPosition($position)
    {
        return $position >= 0 && (is_null($this->max_count) || $position < $this->max_count);
    }

    public function count()
    {
        if(!is_null($this->total_count)) return $this->total_count;
        return -1;
    }
}

