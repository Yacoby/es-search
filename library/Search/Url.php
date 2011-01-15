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

final class Search_Url {
    private $_url;
    private $_parts;
    private $_valid = true;

    /**
     *
     * @param string $url The url. The url can be relative if the current
     *                    url ($currentURL) is set
     * @param Search_Url $currentURL
     */
    public function __construct($url, Search_Url $currentURL = null) {
        if ( $currentURL === null ) {
            $this->constructString($url);
        }else {
            $this->constructRel($url, $currentURL);
        }
    }

    private function constructString($url) {
        if ( !$this->isValidURL($url) ) {
            $this->_valid = false;
        }else {
            $this->_url   = $url;
            $this->_parts = parse_url($url);
        }
    }

    /**
     * Constructs a URL from the current url. Even if the url is ../index.php
     */
    private function constructRel($url, Search_Url  $currentURL) {
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
        return strtolower($this->_parts['host']);
    }
	public function getGet(){
		$hloc = stripos($this->toString(), $this->getHost());
		if ( $hloc === false ){
			throw new Exception('The host was not found in the Url. WTF?');
		}
		$hloc += strlen($this->getHost());
		return substr($this->toString(),$hloc);
	}
    public function toString() {
        return $this->_url;
    }
    public function __toString() {
        return (string)$this->toString();
    }
    protected function isValidURL($url) {
        return Zend_Uri::check($url);
    }

}