<?php

interface Delayed
{
    public function fetch($offset, $limit = null);
}

function array_keys_range_exist($array, $offset, $limit, &$slice = array())
{
    for($i=$offset;$i<$offset+$limit;$i++)
    {
        if(!isset($array[$i])) return false;
        $slice[$i] = $array[$i];
    }
    return true;
}


