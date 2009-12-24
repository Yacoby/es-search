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
 * The aim of this class is two fold, the first is to allow me to store,
 * for a periord of time, search details.
 *
 * The second is to make all user searches hard to guess with access to the data
 * base. You would need the users ip address and A LOT of time.
 */
class Search_User {
    private $_uid;

    /**
     *
     * @var Zend_Session_Namespace
     */
    private $_session;



    private function generateRandomString($length) {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= chr(mt_rand(48, 90));
        }
        return $str;
    }

    public function  __construct() {
        $this->_session = new Zend_Session_Namespace(__CLASS__);

        //if the user hasn't been seen before
        if ( !isset($this->_session->RandomString) ) {
            $this->_session->RandomString = $this->generateRandomString(32);
        }
        $this->_session->setExpirationSeconds(60*60*24*7);

        //
        $this->_uid = md5($_SERVER['REMOTE_ADDR'],$this->_session->RandomString);        
    }

    public function setUserSearched($game, $terms){

    }

    public function setUserSearched($game, $name, $author, $description){
        
    }

}