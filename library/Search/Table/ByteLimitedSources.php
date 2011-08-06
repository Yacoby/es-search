<?php

class Search_Table_ByteLimitedSources extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('ByteLimitedSource',$conn);
    }

    /**
     * Checks to see if a site exists in the database, this is not guarenteed
	 * to be fast as HostName is not a primary key or a much queryed.
     *
     * @param string $host
     * @return bool if the site exists in the database
     */
    public function hasSite($host) {
        return $this->findByHost($host) !== false;
    }

    public function findOneByUpdateRequired(){
        return $this->createQuery()
                    ->select()
                    ->where('next_update < ?', time())
                    ->andWhere('bytes_used < byte_limit')
                    ->andWhere('enabled = ?', true)
                    ->orderBy('next_update ASC')
                    ->limit(1)
                    ->fetchOne();
    }

}
