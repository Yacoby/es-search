<?php
/**
 * This is the class that manages the parsers. It will, given a host, provide
 * the parser class for it
 */
class Search_Parser_Factory {
    private $_iniPath, $_iniDir, $_ini, $_sites;
    private $_hosts = array();

    /**
     * This is an array of the base types of sections in the ini sections mapped
     * to a class name. This is the default class if no other class exists.
     * 
     * @todo This could probably be removed by setting base classes in the 
     *       ini file itself.
     *
     * @var array
     */
    private $_types = array(
        'forum' => 'Search_Parser_Forum',
        'site'  => 'Search_Parser_Site',
    );

    public function  __construct($defaults = null, $ini = null) {
        if ( $defaults !== null && $ini !== null ){
            $this->parseIni($defaults, $ini);
        }
    }

    public function parseIni($defaults, $ini){
        $this->_iniPath = $ini;
        $this->_iniDir  = dirname($ini) . '/';
        $this->setIni(new Search_Parser_Ini($defaults, $ini));
    }

    public function setIni(Search_Parser_Ini $ini){
        $this->_ini     = $ini;
        $this->_sites   = new stdClass();
        foreach ( $this->_ini->sections() as $host => $site ){
            if ( isset($site->implementation) && $site->implementation == 1 ){
                $this->_hosts[]        = $host;
                $this->_sites->{$host} = $site;
            }
        }
    }

    public function getHostsByBaseType($type = null){
        if ( $type && !is_array($type) ){
            $type = array($type);
        }
        $hosts = array();
        foreach ( $this->getHosts() as $host ){
            if ( !$type || in_array($this->findBaseType($host), $type) ){
                $hosts[] = $host;
            }
        }
        return $hosts;
    }

    /**
     * Gets an array of string hosts
     *
     * @return array
     */
    public function getHosts(){
        return $this->_hosts;
    }

    /**
     * Returns true if a host exists.
     *
     * @param string $host
     * @return boolean
     */
    public function hasSite($host) {
        return isset($this->_sites->{$host});
    }

    /**
     * Returns true if there is a parser for the given Url.
     *
     * @param Search_Url $url
     * @return boolean
     */
    public function hasSiteByUrl(Search_Url $url) {
        return $this->hasSite($url->getHost(),$this->_sites);
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
    public function findBaseType($host){
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
     * @param string $host
     * @return Search_Parser_Source_Abstract
     */
    public function getSiteByHost($host){
        if ( $this->hasSite($host) ){
            $details = $this->_sites->{$host};
            $site     = null;
            $baseType = $this->findBaseType($host);

            //check if we have a special site class
            if ( isset($details->site->class) && $details->site->class != ''){
                //load the new site class
                require_once $this->_iniDir . $details->site->location;
                $site = new $details->site->class();
            }else if ( $baseType !== null ) {
                //check its parent. if it has one use that as the search class
                $site = new $this->_types[$baseType]();
            }else{
                throw new Search_Parser_Exception_ClassNotFound(
                                    "No class for type {$details->parent}"
                );
            }
            if ( isset($details->option) && isset($details->option->source) ){
                $site->setOptions((array)$details->option->source);
            }

            //now setup for the page
            if ( isset($details->page->location) ){
                require_once $this->_iniDir . $details->page->location;
            }
            $site->setOption('pageClass', $details->page->class);
            $site->setOption('host', $host);

            return $site;
        }else{
            throw new Search_Parser_Exception_ClassNotFound("Class doesn't exist");
        }
    }

}

?>