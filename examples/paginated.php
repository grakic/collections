<?php

require_once('common.php');


$categories = real_data_loader();

/* Page 3, 10 per page */
$paginated = new PaginatedCollection($categories, 3, 10);
foreach($paginated as $category)
{
    echo "$category\n";
}

/* Total and PagesTotal is not known as the loading is delayed */
echo "\nPage: ".$paginated->getPage()." of ".$paginated->getPagesTotal();
echo "\nTotal: ".$paginated->getTotal();
echo "\nCount: ".count($paginated);
echo "\n";
