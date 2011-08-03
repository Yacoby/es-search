<?php

/**
 * Used to represent something that is closer to the way a site would work
 * with a mod page having links, html associated with it etc
 */
abstract class Search_Parser_Location_AbstractPage extends Search_Parser_Location_Abstract{
    public function __construct($response) {
        //TODO hack for testing. Not good
        if ( $response ){
            parent::__construct($response->url());
        }
    }

    // ------------------------------------------------------------------------
    private $_links = array();
    public function links() {
        return $this->_links;
    }
    protected function addLink($link) {
        $this->_links[] = $link;
    }
    // ------------------------------------------------------------------------

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
