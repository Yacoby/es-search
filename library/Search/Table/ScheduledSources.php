<?php

class Search_Table_ScheduledSources extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('ScheduledSource',$conn);
    }

    /**
     * Checks to see if a site exists in the database, this is not guarenteed
	 * to be fast as HostName is not a primary key or a much queryed.
     *
     * @param string $host
     * @return bool if the site exists in the database
     */
    public function hasName($host) {
        return $this->findByName($host) !== false;
    }

    public function findOneByUpdateRequired(){
        return $this->createQuery()
                    ->select('s.*')
                    ->from('ScheduledSource s')
                    ->innerJoin('s.ModSource ms')
                    ->where('(last_run_time + hours_delta) < ?', time())
                    ->andWhere('ms.scrape = ?', true)
                    ->orderBy('(last_run_time + hours_delta) ASC')
                    ->limit(1)
                    ->fetchOne();
    }

}
