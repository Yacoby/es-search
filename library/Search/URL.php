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
 * A class that holds a url and its parts
 *
 * This class is the exception in its naming, as it doesn't have a name that
 * indicates its directory. This is because it used so much the code would
 * look ugly. (Covering everything in Search_URL). It is therefore included
 * as the start of the application
 *
 *
 */
final class URL {
    private $_url;
    private $_parts;
    private $_valid = true;

    /**
     *
     * @param string $url
     * @param URL $currentURL
     */
    public function __construct($url, URL $currentURL = null) {
        if ( $currentURL == null ) {
            $this->constructString($url);
        }else {
            $this->constructRel($url, $currentURL);
        }
    }

    private function constructString($url) {
        if ( !$this->isValidURL($url) ) {
            $this->_valid = false;
        }else {
            $this->_url = $url;
            $this->_parts = parse_url($url);
        }
    }

    /**
     * Constructs a URL from the current url. Even if the url is ../index.php
     */
    private function constructRel($url, URL  $currentURL) {
        if ( !$currentURL->isValid() ) {
            $this->_valid = false;
        }else if ( !$this->shouldParse($url) ) {
            $this->_valid = false;
        }else {
            try {
                $url = $this->parsePartialURL($url, $currentURL->toString());
                $this->constructString($url);
            }catch(Exception $e ) {
                $this->_valid = false;
            }

        }

    }

    private function shouldParse($url) {
        if ( $url == 'http://' ) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $relative
     * @param string $absolute
     * @return string
     *
     * @todo implement a better parse_url function that fails in a better way
     */
    private function parsePartialURL($relative, $absolute) {
        $p = parse_url($relative);
        if(isset($p["scheme"])) {
            return $relative;
        }

        $host = $user = $scheme = '';
        extract(parse_url($absolute));

        $path = dirname($path);

        if(isset($relative) && strlen($relative) > 0 && $relative{0} == '/') {
            $cparts = array_filter(explode("/", $relative));
        }else {
            $aparts = array_filter(explode("/", $path));
            $rparts = array_filter(explode("/", $relative));
            $cparts = array_merge($aparts, $rparts);
            foreach($cparts as $i => $part) {
                if($part == '.') {
                    $cparts[$i] = null;
                }
                if($part == '..') {
                    $cparts[$i - 1] = null;
                    $cparts[$i] = null;
                }
            }
            $cparts = array_filter($cparts);
        }
        $path = implode("/", $cparts);
        $url = "";
        if($scheme) {
            $url = $scheme."://";
        }
        if($user) {
            $url .= $user;
            if($pass) {
                $url .= ":".$pass;
            }
            $url .= "@";
        }
        if($host) {
            $url .= $host."/";

        }
        $url .= $path;
        return $url;
    }


    public function isValid() {
        return $this->_valid;
    }
    public function getHost() {
        assert($this->_valid);
        return $this->_parts['host'];
    }
    public function toString() {
        return $this->_url;
    }
    public function __toString() {
        return $this->toString();
    }
    protected function isValidURL($url) {
        return Zend_Uri::check($url);
    }

}