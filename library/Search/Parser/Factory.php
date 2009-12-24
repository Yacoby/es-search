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
 * Singleton class, Responsible for loading classes.
 */
class Search_Parser_Factory {
    private $_sites;

    /**
     * Loads all site files
     */
    private function __construct() {
        $this->includeAllInDir(dirname(__FILE__).'/Site');
        $this->_sites = $this->getSitesClasses();
    }

    /**
     * Includes all files in a directory (Not any sub directories!)
     * 
     * @param string $d The directory to search for files to include
     */
    private function includeAllInDir($d){
       $h = opendir($d);
        while ($f = readdir($h)) {
            if ( is_file($d."/".$f)
                && $f != '.'
                && $f != '..' ) {
                include ($d.'/'.$f);
            }
        }
        closedir($h);
    }

    /**
     * Gets from the list of currently declared classes an array of sites
     * that inherit from page and have a host defined
     *
     * @return array a associative array of host -> class
     */
    private function getSitesClasses(){
        $sites = array();
        $classes = get_declared_classes();
        foreach ( $classes as $c ){
            if ( is_subclass_of($c, 'Search_Parser_Site') ){
                $host = call_user_func(array($c, 'getHost'));
                if ( $host !== null ){
                    $sites[$host] = $c;
                }
            }
        }
        return $sites;
    }

    public function _sites(){
        return $this->_sites;
    }

    /**
     * @staticvar Search_Parser_Factory $pf
     * @return Search_Parser_Factory
     */
    public static function getInstance() {
        static $pf = null;
        if ( !$pf ){
            $pf = new self();
        }
        return $pf;
    }

    public function hasSite($host) {
        return array_key_exists($host,$this->_sites);
    }
    public function hasSiteByURL($url) {
        return $this->hasSite($url->getHost(),$this->_sites);
    }

    /**
     *
     * @param URL $url
     * @return Site
     */
    public function getSiteByURL(URL $url) {
        return $this->getSiteByHost($url->getHost());
    }
    public function getSiteByHost($host){
        if ( $this->hasSite($host) ){
            return new $this->_sites[$host];
        }
        throw new Exception("Class doesn't exist");
    }

}
?>
