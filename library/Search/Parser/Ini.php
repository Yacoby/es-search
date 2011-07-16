<?php

class Search_Parser_Ini {
    private $_iniSections = array();

    public function __construct() {
        $args = func_get_args();

        if ( func_num_args() ) {
            $this->parse(array_shift($args));

            foreach ( $args as $arg ) {
                $this->merge($arg);
            }
        }
    }

    /**
     *
     * @param string $ini The path of the ini path or the ini string
     */
    public function parse($ini) {
        $this->_iniSections = $this->parseUsingFunction(
                                            $this->selectFunction($ini),
                                            $ini
                              );
    }

    public function merge($ini, $overwrite = true) {
        $f = $this->selectFunction($ini);
       $this->mergeIni($this->_iniSections,
                       $this->parseUsingFunction($f, $ini),
                       $overwrite);
    }

    public function section($name) {
        return $this->_iniSections->{$name};
    }
    public function hasSection($name){
        return isset($this->_iniSections->{$name});
    }

    /**
     * @return stdClass
     */
    public function sections() {
        return $this->_iniSections;
    }

    private function parseUsingFunction($f, $ini) {
        $parsedIni = $f($ini, true);

        if ( $parsedIni === false ){
            throw new Exception("Failed to parse ini {$ini}");
        }

        $sections  = new stdClass();

        foreach ( $parsedIni as $sectionName => $sectionValues ) {
            list($name, $parent) = $this->parseSectionName($sectionName);
            $section             = $this->parseSection($sectionValues);

            //merge parent
            if ( $parent !== null ){
                $section->parent = $parent;

                //if the superclass is in the same file, it won't have been added
                //to the list of sections yet. Hence we need to check the $sections
                //variable.
                if ( !$this->hasSection($section->parent) &&
                     !isset($sections->{$section->parent}) ){
                    throw new Exception("{$section->parent} doesn't exist but is required");
                }

                $secToMerge = isset($sections->{$section->parent}) 
                                     ? $sections->{$section->parent} 
                                     : $this->section($section->parent);

                $this->mergeIni($section,
                                $secToMerge,
                                false);
            }
            $sections->{$name} = $section;
        }

        return $sections;
    }

    private function selectFunction($ini) {
        return file_exists($ini) ? 'parse_ini_file' : 'parse_ini_string';
    }

    /**
     *
     * @param string $name A string in the format .*:.*
     * @return an array of the first part before the colon and then the part after
     *          the colon.
     */
    private function parseSectionName($name) {
        $i = strrpos($name, ':');

        if ( $i === false ) {
            return array($name, null);
        }

        return array(
                substr($name, 0, $i),
                substr($name,$i+1)
        );
    }

    private function parseSection($section) {
        $values = new stdClass();
        foreach ( $section as $key => $value ) {
            $arrayPath = explode(':', $key);
            $this->arrayPathSet($values, $arrayPath, $value);
        }
        return $values;
    }

    /**
     * Sets a value in an array, given the value of the path
     *
     * @param array|stdClass $array
     * @param array $path
     * @param $value
     */
    private function arrayPathSet($array, $path, $value) {
        $thisKey = array_shift($path);
        if ( empty($path) ) {
            $array->{$thisKey} = $value;
            return;
        }
        if ( !isset($array->{$thisKey}) ) {
            $array->{$thisKey} = new stdClass();
        }

        $this->arrayPathSet($array->{$thisKey}, $path, $value);
    }

    /**
     * This is an implementation of array_merge_recursive for stdClass.
     *
     * @param stdClass $a
     * @param stdClass $b
     * @param boolean $overwrite
     * @return stdClass
     */
    private function mergeIni(stdClass $a, stdClass $b, $overwrite = true) {
        foreach ( $b as $key => $value ) {
            if ( $value instanceof stdClass ) {
                if ( !isset($a->{$key}) ) {
                    $a->{$key} = new stdClass();
                }
                $this->mergeIni($a->{$key}, $b->{$key}, $overwrite);
            }else {
                if ( $overwrite || !isset($a->{$key}) ) {
                    $a->{$key} = $b->{$key};
                }
            }
        }
    }
}