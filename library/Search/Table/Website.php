<?php
/* l-b
 * This file is part of ES Search.
 * 
 * Copyright (c) 2009 Jacob Essex
 * 
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ES Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with ES Search. If not, see <http://www.gnu.org/licenses/>.
 * l-b */

/**
 * Allows access to website data. It also keeps website limits updated
 */
class Search_Table_Website extends Zend_Db_Table_Abstract {
    protected $_name    = 'Website';
    protected $_primary = 'HostName';

    /**
     * Updates all limits that haven't been updated within the last 30 minutes,
     * but are over the byte limit
     *
     * @todo magic number
     */
    private function updateAllLimits() {
        $cols = array('HostName', 'ByteLimit', 'BytesUsed', 'BytesLastUpdated');
        $select = $this->select()
                ->from($this, $cols)
                ->where('BytesUsed >= ByteLimit')
                ->where('BytesLastUpdated<?', time()-60*30);

        $results = $this->fetchAll($select);
        foreach ( $results as $result ) {
            $this->updateLimit($result->toArray());
        }
    }

    /**
     * Updates the limits for a single site
     *
     * @param array $data
     */
    private function updateLimit(array $data) {
        assert(array_key_exists('BytesLastUpdated', $data));
        assert(array_key_exists('BytesUsed', $data));
        assert(array_key_exists('ByteLimit', $data));
        assert(array_key_exists('HostName', $data));

        $ud = self::getUpdatedDetails(
                $data['BytesLastUpdated'],
                $data['BytesUsed'],
                $data['ByteLimit']
        );

        $where = $this->getAdapter()
                ->quoteInto('HostName=?',$data['HostName']);
        
        $this->update($ud, $where);
    }


    /**
     * Has a one in twenty chance of updating the limits to ensure the current byte
     * limit has been reduced.
     *
     * @todo fix magic number
     */
    public function __construct() {
        parent::__construct();

        $random = rand(0, 20);
        if ( $random == 0 ) {
            $this->updateAllLimits();
        }
    }

    /**
     * Checks to see if a site exists in the database
     *
     * @param string $host
     * @return bool if the site exists in the database
     */
    public function hasSite($host) {
        $select = $this->select()
                ->from($this, 'COUNT(*) AS num')
                ->where('HostName=?', $host);
        return $this->fetchRow($select)->num > 0;
    }

    /**
     * Adds a site to the database, but will not check if it already exists
     *
     * @param string $host
     * @param int $nextUpdate
     */
    public function addSite($host, $nextUpdate = 1) {
        $params = array(
                'HostName'          => $host,
                'NextUpdate'        => (int)$nextUpdate,
                'BytesLastUpdated'  => time()
        );
        $this->insert($params);

    }

    /**
     * Sets the maximum number of bytes that can be used per day for a host
     *
     * This shouldn't be chagned, it is set via a cronjob one a day from the files
     * so if it needs to be changed for any length of time, it should be done so
     * in the files
     *
     * @param string $host
     * @param int $limit
     */
    public function setByteLimit($host, $limit) {
        $where = $this->getAdapter()->quoteInto('HostName=?',$host);
        $data = array(
                'ByteLimit' => $limit
        );

        $this->update($data, $where);
    }


    /**
     * Gets a set of limits. This does extra processing to ensure that the
     * limits returned are fully up to date, so the values returned will not
     * reflect the values in the database.
     *
     * @param string $host
     * @return array
     */
    public function getLimits($host) {
        $select = $this->select()
                ->where('HostName=?', $host)
                ->limit(1);

        $results = $this->fetchAll($select);

        if ( $results->count() == 0 ) {
            throw new Exception("Not enough data in database");
        }

        $result = $results->getRow(0);

        return self::getUpdatedDetails(
                $result->BytesLastUpdated,
                $result->BytesUsed,
                $result->ByteLimit
        );

    }

    /**
     * Increases the amount of bandwidth that has been used
     *
     * This will throw if the site doesn't exist
     *
     * @param string $host
     * @param int $bytes
     *
     * @todo maybe needs a better name.
     */
    public function increaseUsage($host, $bytes) {
        if ( !is_numeric($bytes) ) {
            throw new Exception("Invalid args");
        }

        $select = $this->select()
                ->from($this, array('BytesUsed'))
                ->where('HostName=?', $host);

        $results = $this->fetchAll($select);

        if ( $results->count() == 0 ) {
            throw new Exception("Invalid args. No site with this name");
        }

        $args = array(
                'BytesUsed' => (int)($results->getRow(0)->BytesUsed + (int)$bytes)
        );

        $where = $this->getAdapter()->quoteInto('HostName=?',$host);
        $this->update($args, $where);

    }

    /**
     *
     * @param int $lastUpdateTime The time (unix time stamp) the bytes were last updated
     * @param int $current The current byte usage
     * @param int $limit The byte limit
     * @return array the new limits
     */
    static private function getUpdatedDetails($lastUpdateTime, $current, $limit) {
        assert(is_numeric($lastUpdateTime));
        assert(is_numeric($current));
        assert(is_numeric($limit));

        $perSec = $limit / 60 / 60 / 24; //get the number of pages we can dl per second (normally 0.xxx);
        $change = $perSec * ( time() - $lastUpdateTime ); //work how many pages we have left has changed since we last did this
        $changeF = floor($change); //only deal in whole numbers, so floor this to get an int
        $changeRem = $change - $changeF; //get the amount left over
        $current -= $changeF; //increase the pages remining by the int
        if ( $current < 0 ) { //but make sure we don't let it run over the max
            $current = 0;
        }
        //work out how many seconds the amount left over is, and remove it from the time, so we can deal deal with it next time.
        //this ensures that we don't end up losing/gaining pages.
        assert($perSec!=0);
        $lastUpdateTime = time() - ceil(( $changeRem / $perSec)) ;
        return array(
                'BytesLastUpdated' => $lastUpdateTime,
                'BytesUsed' => $current,
                'ByteLimit' => $limit,
        );
    }

    /**
     * gets a single site that needs upddating or null if no site does.
     *
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function getSiteNeedingUpdate() {
        $select = $this->select()
                ->where('NextUpdate < '.time())
                ->where('Enabled = 1')
                ->order('NextUpdate ASC')
                ->limit(1);
        return $this->fetchRow($select);
    }

    /**
     * Sets a site as having run its updates, so that it will next run its updates
     * in $days time.
     *
     *
     * @param string $host the host
     * @param int $days the next update time
     *
     * @todo magic number
     */
    public function setSiteUpdated($host, $days = 1) {
        $params = array(
                'NextUpdate' => time()+((60*60*24)*$days)
        );

        $where = $this->getAdapter()->quoteInto("HostName=?", $host);
        $this->update($params, $where);
    }
}
