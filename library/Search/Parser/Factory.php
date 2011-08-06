<?php
/**
 * This is the class that manages the parsers. It will, given a indentifier
 * provide the parser class for it
 */
class Search_Parser_Factory {
    private $_iniDir, $_ini; 
    
    private $_parsers;
    /**
     * A list of identifiers for the parsers
     */
    private $_names = array();

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
        'forum'     => 'Search_Parser_Forum',
        'site'      => 'Search_Parser_Site',
        'scheduled' => '',
    );

    public function  __construct($defaults = null, $ini = null) {
        if ( $defaults !== null && $ini !== null ){
            $this->parseIni($defaults, $ini);
        }
    }

    public function parseIni($defaults, $ini){
        $this->_iniDir  = dirname($ini);

        //TODO this is an ugly ugly hack
        $incPath = get_include_path();
        if ( stripos($incPath, $this->_iniDir) === false ){
            set_include_path($incPath . PATH_SEPARATOR . $this->_iniDir);
        }
        $this->setIni(new Search_Parser_Ini($defaults, $ini));
    }

    public function setIni(Search_Parser_Ini $ini){
        $this->_ini     = $ini;
        $this->_parsers = new stdClass();
        foreach ( $this->_ini->sections() as $name => $parser){
            if ( isset($parser->implementation) &&
                 $parser->implementation == 1 ){
                $this->_names[]          = $name;
                $this->_parsers->{$name} = $parser;
            }
        }
    }

    public function getNamesByBaseType($type = null){
        if ( $type && !is_array($type) ){
            $type = array($type);
        }
        $hosts = array();
        foreach ( $this->getNames() as $host ){
            if ( !$type || in_array($this->findBaseType($host), $type) ){
                $hosts[] = $host;
            }
        }
        return $hosts;
    }

    /**
     * Gets an array of string parser names 
     *
     * @return array
     */
    public function getNames(){
        return $this->_names;
    }

    /**
     * Returns true if a host exists.
     *
     * @param string $name
     * @return boolean
     */
    public function hasParser($name) {
        return isset($this->_parsers->{$name});
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

    public function getScheduledByName($name){
        if ( $this->hasParser($name) ){
            $details = $this->_parsers->{$name};

            //setup for the parser
            if ( isset($details->location) ){
                require_once $this->_iniDir . '/' . $details->location;
            }
            $parser = new $details->class();
            if ( isset($details->option) ){
                $parser->setOptions((array)$details->option);
            }

            return $parser;
        }else{
            throw new Search_Parser_Exception_ClassNotFound("Class doesn't exist");
        }

    }

    /**
     * @param string $host
     * @return Search_Parser_Source_Abstract
     */
    public function getSiteByHost($host){
        if ( $this->hasParser($host) ){
            $details = $this->_parsers->{$host};
            $baseType = $this->findBaseType($host);

            //check if we have a special site class
            if ( isset($details->location) && $details->location != '' ){
                require_once $this->_iniDir . '/' . $details->location;
            }
            $site = new $details->class();

            if ( isset($details->option) && isset($details->option) ){
                $site->setOptions((array)$details->option);
            }

            return $site;
        }else{
            throw new Search_Parser_Exception_ClassNotFound("Class doesn't exist");
        }
    }

}
