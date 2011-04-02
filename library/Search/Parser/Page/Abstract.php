<?php

abstract class Search_Parser_Page_Abstract {

    private $_links = array();
    private $_mods  = array();

    protected $_url, $_html;

    public function __construct($url, $html) {
        $this->_url  = $url;
        $this->_html = $html;
    }

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
    /**
     * This function is used before parsing and logging in to check that the page is
     * at least roughly valid. A basic check should be done to see if the page at least
     * looks correct.
     *
     * This was implemented due to tesnexus not returning
     * 404 when the mod didn't exist, just a plain page saying 'this mod isn't valid'
     *
     * @return bool
     */
    public function isValidPageBody() {
        return true;
    }

    abstract public function parsePage($client);
}