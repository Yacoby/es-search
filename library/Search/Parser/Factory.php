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
 * This is the class that manages the parsers. It will, given a host, provide
 * the parser class for it
 */
class Search_Parser_Factory {

    /**
     * An associative array of host -> class of every defined parser
     *
     * @var array
     */
    private $_sites;

    /**
     * Loads all site files
     *
     * @param $file
     *      If set, this is the location of the parsers.
     */
    public function __construct($files = null) {
        $files = $files ? $files : dirname(__FILE__).'/Site';
        $this->includeAllInDir($files);
        $this->_sites = $this->getSitesClasses();
    }

    /**
     * Includes all files in a directory (Not any sub directories!)
     * 
     * @param string $d The directory to search for files to include
     */
    private function includeAllInDir($d){
        foreach ( glob($d.'/*.php') as $file ){
            //this must be require once. If you construct two instances of this
            //class it is possible to load the same file twice
            require_once $file;
        }
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
                    $sites[$host] = new $c();
                }
            }
        }
        return $sites;
    }

    /**
     * Gets a list of all sites registered with this factory
     *
     * @return array
     */
    public function getSites(){
        return $this->_sites;
    }

    public function hasSite($host) {
        return array_key_exists($host,$this->_sites);
    }
    public function hasSiteByURL($url) {
        return $this->hasSite($url->getHost(),$this->_sites);
    }

    /**
     *
     * @param Search_Url $url
     * @return Site
     */
    public function getSiteByURL(Search_Url $url) {
        return $this->getSiteByHost($url->getHost());
    }
    /**
     *
     * @param <type> $host
     * @return Search_Parser_Site
     */
    public function getSiteByHost($host){
        if ( $this->hasSite($host) ){
            return new $this->_sites[$host];
        }
        throw new Search_Parser_Exception_ClassNotFound("Class doesn't exist");
    }

}
?>
