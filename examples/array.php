<?php

require_once('common.php');


$categories = real_data_loader();

foreach($categories as $category)
{
    echo "$category\n";
}

echo "\nCount: ".count($categories);
echo "\n";
