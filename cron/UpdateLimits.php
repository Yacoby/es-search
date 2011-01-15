<?php

/*
 * This should be run every 10 minutes or so. It updates all the sites limits
 */

require '../AppLoader.php';
$app = createApplication(realpath(dirname(__FILE__) . '/Bootstrap/Bootstrap.php'));

$table = new Search_Table_Sites();
$sites = $table->createQuery()
               ->where('bytes_used >= byte_limit')
               ->execute();

/**
 * The limit always returns correct...ish data as it calculatees them exactly
 * when pulled from the database. So all we need to do is save it so the
 * changes are reflected in the database.
 */
foreach ($sites as $site) {
    //basically, makes it diry:
    $site->bytes_last_updated += 1;
    $site->bytes_used         += 1;

    
    $site->save();
}