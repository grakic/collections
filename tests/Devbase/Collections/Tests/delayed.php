<?php

require_once('common.php');


$delayed = new DelayedCollection('real_data_loader', 10);
foreach($delayed as $category)
{
    echo "$category\n";
}

/* Total is known only after the loop */
echo "\nTotal: ".count($delayed);
echo "\n";
