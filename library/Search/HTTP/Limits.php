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
 * Facade(?) for the WebsiteTable class, but only exposes the limit related data
 *
 * Hangover from an older version impelemted to maintain compatibility.
 *
 */
class Search_HTTP_Limits {

    /**
     *
     * @var WebsiteTable
     */
    private $_sites;

    function __construct(Search_Table_Website $website = null) {
        if ( !$website ) {
            $website = new Search_Table_Website();
        }
        $this->_sites = $website;
    }

    /**
     * Updates limits, tries to use as few queries as possible
     * @deprecated
     */
    public function updateAllLimits() {
        assert(false);
    }


    /**
     * @param URL $url
     * @return bool
     */
    public function hasLimits(URL $url) {
        assert($url->isValid());
        return $this->_sites->hasSite($url->getHost());
    }

    /**
     * @param URL $url
     * @return array
     */
    public function getLimits(URL $url) {
        assert($url->isValid());
        return $this->_sites->getLimits($url->getHost());
    }

    /**
     * @param URL $url
     * @return bool
     */
    public function canGetPage(URL $url) {
        assert($url->isValid());

        $byteDetails = $this->getLimits($url);
        return $byteDetails['BytesUsed'] <  $byteDetails['ByteLimit'];
    }



    /**
     * Updates the database bytes used
     *
     * @param URL $url
     * @param int $size
     */
    public function addRequesedPage(URL $url, $size) {
        assert($url->isValid());
        $this->_sites->increaseUsage($url->getHost(), $size);
    }

}
