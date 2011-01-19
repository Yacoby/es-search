<?php

/*
 * This should be run every 30 minutes or so. It:
 *  * updates all the sites limits
 *  * cleans up the lists of searches
 *  * removes old bans
 */

require '../AppLoader.php';
$app = createApplication(realpath(dirname(__FILE__) . '/Bootstrap/Bootstrap.php'));
try{
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

    /**
     * This removes all searches that are older than a day
     * and ensures there are at maximum 50 searches in the database
     */
    $max = Doctrine_Query::create()
                ->select('MAX(id) as max_id')
                ->from('SearchHistory')
                ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    Doctrine_Query::create()
                ->delete()
                ->from('SearchHistory')
                ->where('id < ?', $max - 50)
                ->orWhere('search_time < ?', time() - 60 * 60 * 24)
                ->execute();

    /**
     * Cleans up bans
     */
    Doctrine_Query::create()
                ->delete()
                ->from('HistoryBanned')
                ->where('banned_time < ?', time() - 60 * 60 * 24 * 31)
                ->execute();
}catch(Exception $e){
    Search_Logger::err('Unhandled Exception:' . $e->getMessage());
}