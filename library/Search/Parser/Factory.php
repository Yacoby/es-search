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
    private $_iniPath, $_iniDir, $_ini, $_sites;

    private $_types = array(
        'forum' => 'Search_Parser_Forum',
        'site'  => 'Search_Parser_Site',
    );

    public function  __construct($defaults = null, $ini = null) {
        if ( $defaults !== null && $ini !== null ){
            $this->parse($defaults, $ini);
        }
    }

    public function parse($defaults, $ini){
        $this->_iniPath = $ini;
        $this->_iniDir  = dirname($ini) . '/';
        $this->_ini     = new Search_Parser_Ini($defaults, $ini);
        $this->_sites     = new stdClass();
        foreach ( $this->_ini->sections() as $host => $site ){
            if ( $site->implementation == 1 ){
                $this->_sites->{$host} = $site;
            }
        }
    }

    /**
     * Gets a list of all sites registered with this factory
     *
     * @return array
     */
    public function getSites(){
        return $this->_sites;
    }
    public function getSite($host){
        return isset($this->_sites->{$host}) ? $this->_sites->{$host} : null;
    }

    public function hasSite($host) {
        return isset($this->_sites->{$host});
    }
    public function hasSiteByUrl($url) {
        return $this->hasSite($url->getHost(),$this->getSites());
    }

    /**
     *
     * @param Search_Url $url
     * @return Search_Parser_Source_Abstract
     */
    public function getSiteByUrl(Search_Url $url) {
        return $this->getSiteByHost($url->getHost());
    }

    /**
     * Given a host, this finds the first ancestor of that host that is in
     * the types array or null if there isn't one
     * 
     * @param string $host
     * @return string|null
     */
    private function findBaseType($host){
        if ( !$this->_ini->hasSection($host) ){
            throw new Exception('Cannot find given host');
        }
        $details = $this->_ini->section($host);

        if ( !isset($details->parent) ){
            return null;
        }else if ( array_key_exists($details->parent, $this->_types) ){
            return $details->parent;
        }
        
        return $this->findBaseType($details->parent);
    }
    /**
     *
     * @param string $host
     * @return Search_Parser_Source_Abstract
     */
    public function getSiteByHost($host){
        
        if ( $this->hasSite($host) ){
            $details = $this->getSite($host);
            $site     = null;
            $baseType = $this->findBaseType($host);

            //check if we have a special site class
            if ( $details->site->class != ''){
                //load the new site class
                require_once $this->_iniDir . $details->site->location;
                $site = new $details->site->class();
            }else if ( $baseType !== null ) {
                //check its parent. if it has one use that as the search class
                $site = new $this->_types[$baseType]();
            }else{
                throw new Search_Parser_Exception_ClassNotFound("No class for type {$details->parent}");
            }
            if ( isset($details->option) && isset($details->option->source) ){
                $site->setOptions((array)$details->option->source);
            }

            //now setup for the page
            require_once $this->_iniDir . $details->page->location;
            $site->setOption('pageClass', $details->page->class);
            $site->setOption('host', $host);

            return $site;
        }else{
            echo "Looking for {$host}\n";
            var_dump($this->getSites());

            throw new Search_Parser_Exception_ClassNotFound("Class doesn't exist");
        }
    }

}
?>
