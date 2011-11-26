<?php

require_once('common.php');


$delayed = new DelayedCollection('real_data_loader', 10);

/* Page 3, 15 per page */
$paginated = new PaginatedCollection($delayed, 3, 15);
foreach($paginated as $category)
{
    echo "$category\n";
}

/* Total and PagesTotal is not known as the loading is delayed */
echo "\nPage: ".$paginated->getPage()." of ".$paginated->getPagesTotal();     
echo "\nTotal: ".$paginated->getTotal();
echo "\nCount: ".count($paginated);
echo "\n";

