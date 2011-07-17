<?php

/**
 * Represents somewhere that may have 0..n mods but should mainly have 1..n mods
 */
abstract class Search_Parser_Location_Abstract {
    private $_mods  = array();
    protected $_url;

    public function __construct($url) {
        $this->_url  = $url;
    }

    public function mods() {
        return $this->_mods;
    }

    protected function addMod($mod) {
        $this->_mods[] = $mod;
    }

}
