<?php

require_once('../src/common.php');
require_once('../src/delayed.php');
require_once('../src/cached.php');
require_once('../src/paginated.php');

/* Dummy data source. See this as database or web service */
define('REAL_DATA_LOADER_ITEMS', 100);
function real_data_loader($offset = null, $limit = null)
{
        echo " -> real_data_loader($offset, $limit)\n";
        $categories = array();
        if(is_null($offset)) {
            /* Get all */
            for($i=0;$i<REAL_DATA_LOADER_ITEMS;$i++) $categories[] = "Category $i";
        } else {
            /* Get partial */
            $limit = min($offset+$limit, REAL_DATA_LOADER_ITEMS);
            for($i=$offset;$i<$limit;$i++) $categories[] = "Category $i";
        }
	return $categories;
}
