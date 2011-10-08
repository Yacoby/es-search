<?php

/*
 * This should be run every 30 minutes or so. It:
 *  * updates all the sites limits
 *  * cleans up the lists of searches
 *  * removes old bans
 */
function getUpdatedDetails($lastUpdateTime, $current, $limit) {
    assert(is_numeric($lastUpdateTime));
    assert(is_numeric($current));
    assert(is_numeric($limit));

    if ( $limit == 0 ){
        return array(
            'bytes_last_updated' => time(),
            'bytes_used'         => 0,
        );
    }

     //get the number of pages we can dl per second (normally 0.xxx);
    $perSec    = $limit / 60 / 60 / 24;

    //work how many pages we have left has changed since we last did this
    $change    = $perSec * ( time() - $lastUpdateTime );

    //only deal in whole numbers, so floor this to get an int
    $changeF   = floor($change);

    //get the amount left over
    $changeRem = $change - $changeF;

    //increase the pages remining by the int
    $current  -= $changeF;

    //but make sure we don't let it run over the max
    if ($current < 0){
        $current = 0;
    }

    //work out how many seconds the amount left over is, and remove it from
    // the time, so we can deal deal with it next time.
    //this ensures that we don't end up losing/gaining pages.
    assert($perSec != 0);
    $lastUpdateTime = time();// - ceil(( $changeRem / $perSec));
    return array(
        'bytes_last_updated' => $lastUpdateTime,
        'bytes_used'         => $current,
    );
}



require realpath(dirname(__FILE__).'/../AppLoader.php');

$app = createApplication(realpath(dirname(__FILE__) . '/Bootstrap/Bootstrap.php'));
try{
    //$table = new Search_Table_Sites();
    //$sites = $table->createQuery()
    $sites = Doctrine_Query::create()
                   ->select()
                   ->from('ByteLimitedSource b, b.ModSource s')
                   ->where('b.bytes_used >= b.byte_limit')
                   ->andWhere('s.scrape = True')
                   ->execute();

    foreach ($sites as $site) {
        $details = getUpdatedDetails($site->bytes_last_updated,
                                     $site->bytes_used,
                                     $site->byte_limit);

        $site->bytes_last_updated = $details['bytes_last_updated'];
        $site->bytes_used         = $details['bytes_used'];
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
     * Deletes old uninportant logs
     */
    Doctrine_Query::create()
                ->delete()
                ->from('Log')
                ->where('timestamp < now() - interval \'1 day\'')
                ->andWhere('level>=6')
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
