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
 * requests and thereby increse the number of mods it is possible to index
 */
class Search_Table_CookieJar extends Zend_Db_Table_Abstract {
    protected $_name = 'CookieJar';
    protected $_primary = array('Domain', 'Value');


    /**
     * Gets an array of Zend_HTTP_Cookie objects that match a domain, but
     * it will not return cookies that have expired
     *
     * @param $domain the domain to retrive the cookies for
     *
     * @todo domain matching doesn't match RFC 2965
     */
    public function getCookies($domain) {
        $select = $this->select()
                ->where('Domain LIKE ?', '%'.$domain)
                ->where('Expires>?', time());

        $cookiePacket = array();
        $result = $this->fetchAll($select);
        foreach ( $result as $row ) {
            $cookiePacket[] = new Zend_Http_Cookie(
                    $row->Name,
                    $row->Value,
                    $row->Domain,
                    $row->Expires,
                    $row->Path,
                    $row->Secure
            );
        }
        return $cookiePacket;
    }

    /**
     * Adds a set of cookies to the domain, this will delete expired cookies
     *
     *
     * @param $cookiePacket a array of Zend_HTTP_Cookie objects
     */
    public function addOrUpdateCookies(array $cookiePacket) {
        foreach ( $cookiePacket as $cookie ) {
            $this->delete(
                    $this->getAdapter()->quoteInto('Domain=?', $cookie->getDomain())
                    . ' AND '
                    . $this->getAdapter()->quoteInto('Name=?', $cookie->getName())
            );

            if ( $cookie->isExpired() ) {
                continue;
            }
            
            $rawCookie = array(
                    'Name'      => $cookie->getName(),
                    'Value'     => $cookie->getValue(),
                    'Domain'    => $cookie->getDomain(),
                    'Expires'   => $cookie->getExpiryTime(),
                    'Path'      => $cookie->getPath(),
                    'Secure'    => $cookie->isSecure()
            );
            $this->insert($rawCookie);
        }

    }


}