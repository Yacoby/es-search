<?php

abstract class Search_Parser_Source_Abstract {

    private $_options = array();

    public function getPageClass() {
        return $this->getOption('pageClass');
    }

    public function setOptions(array $options) {
        foreach ( $options as $key => $value ) {
            $this->_options[$key] = $value;
        }
    }
    public function setOption($key, $value) {
        $this->_options[$key] = $value;
    }
    public function getOption($key) {
        return $this->_options[$key];
    }
    public function hasOption($key){
        return array_key_exists($key, $this->_options);
    }

}
