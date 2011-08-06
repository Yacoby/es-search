<?php

/**
 * This is what is expected back from the scrape function
 */
class Search_Parser_ScrapeResult {

    private $_mods  = array();
    public function mods() {
        return $this->_mods;
    }
    protected function addMod($mod) {
        $this->_mods[] = $mod;
    }

}
