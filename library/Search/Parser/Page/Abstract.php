<?php

abstract class Search_Parser_Page_Abstract {

    private $_links = array();
    private $_mods  = array();

    public function mods() {
        return $this->_mods;
    }
    public function links() {
        return $this->_links;
    }

    protected function addLink($link) {
        $this->_links[] = $link;
    }
    protected function addMod($mod) {
        $this->_mods[] = $mod;
    }

    abstract public function isModNotFoundPage($client);

    abstract public function parsePage($client);



}