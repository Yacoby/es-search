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
 * Facade(?) for Search_Table_ModSites, but only exposes the limit related data
 *
 * Hangover from an older version impelemted to maintain compatibility. Worth
 * considering if this should be removed all together
 */
class Search_HTTP_Limits {

    /**
     * @var Search_Table_ModSites
     */
    private $_sites;

    /**
     * @param Search_Table_ModSites|null $website The class for handling the
     *                                            site data
     */
    function __construct(Search_Table_Sites $website = null) {
        $this->_sites = $website ? $website : new Search_Table_Sites();
    }

    /**
     * Updates limits, tries to use as few queries as possible
     * @deprecated
     */
    public function updateAllLimits() {
        assert(false);
    }


    /**
     * @param Search_Url $url
     * @return bool
     */
    public function hasLimits(Search_Url $url) {
        assert($url->isValid());
        return $this->_sites->hasSite($url->getHost());
    }

    /**
     *
     * @param Search_Url $url
     * @return array
     */
    /*
    public function getLimits(Search_Url $url) {
        assert($url->isValid());

        $site = $this->_sites->getByHost($url->getHost());
        return $site->ByteLimit;
    }
     */

    /**
     * Checks if there is any bytes left to request a page.
     * This doesn't check if the page will go over that limit (as we don't know
     * how big it is) so this will always lead to it slightly overreaching the
     * limit all the time.
     *
     * @param Search_Url $url The url of the page.
     * @return bool
     */
    public function canGetPage(Search_Url $url) {
        assert($url->isValid());

        $site = $this->_sites->getByHost($url->getHost());
        return $site->BytesUsed < $site->ByteLimit;
    }


    /**
     * Updates the database bytes used
     *
     * @param Search_Url $url The url of the page
     * @param int $size The size in bytes of the page.
     */
    public function addRequesedPage(Search_Url $url, $size) {
        assert($url->isValid());
        assert((int)$size == $size);

        $site = $this->_sites->findOneByHost($url->getHost());
        $site->bytes_used += (int)$size;
        $site->save();
    }

}
