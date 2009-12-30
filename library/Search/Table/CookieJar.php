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
 * Table that holds cookie values used for parsing to extend the login
 * sessions over mutliple requests. This is to reduce the number of page
 * requests and thereby increse the number of mods it is possible to index.
 *
 * This doesn't work perfectly, as they are stored on the database by domain,
 * and no matching is done on the db, which could lead to issues with subdomains
 * if the cookies needed to carry over. This is not an issue in this case though
 * so is not flagged as todo.
 */
class Search_Table_CookieJar extends Zend_Db_Table_Abstract {
    protected $_name    = 'CookieJar';
    protected $_primary = array('Domain');


    /**
     * Gets an array of Zend_HTTP_Cookie objects that match a domain.
     *
     * @param $domain the domain to retrive the cookies for
     * @return arrray
     */
    public function getCookies($domain) {
        $select = $this->select()
                ->from($this, 'Data')
                ->where('Domain=?', $domain);

        $cookiePacket = array();
        $result = $this->fetchRow($select);
        return $result ? unserialize($result->Data) : array();
    }

    /**
     * Adds a set of cookies to the domain, this will delete expired cookies
     *
     *
     * @param $cookiePacket a array of Zend_HTTP_Cookie objects
     * @param $domain the domain that the cookies should come from
     */
    public function addOrUpdateCookies(array $cookiePacket, $domain) {
        $this->delete(
                $this->getAdapter()->quoteInto('Domain=?', $domain)
        );
        
        foreach ( $cookiePacket as $key => $cookie ) {
            if ( $cookie->isExpired() ) {
                unset($cookiePacket[$key]);
            }
        }

        $data = array(
            'Domain'    => $domain,
            'Data'      => serialize($cookiePacket)
        );

        $this->insert($data);

    }


}