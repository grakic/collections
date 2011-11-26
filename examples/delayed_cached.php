<?php

require_once('common.php');

/**
 * Usage php delayed_cached.php [SESSION ID]
 *
 * SessionCachedCollection is a simple example how to use
 * the CachedCollection for deriving your own classes.
 */
$session_id = isset($argv[1]) ? $argv[1] : null;

$delayed = new DelayedCollection('real_data_loader', 10);

$cached = new SessionCachedCollection($delayed, "categories", $session_id);
foreach($cached as $category)
{
    echo "$category\n";
}

/* Total is known only after the loop */
echo "\nTotal: ".count($delayed);
echo "\nSession ID: ".session_id();
echo "\nNow start with php delayed_cached.php ".session_id();
echo "\n";
