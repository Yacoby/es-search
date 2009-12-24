<?php /* l-b
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
 * l-b */ ?>

<?php

/**
 * Superclass for a site, all sites should inherit from this class, else they
 * will not get picked up by the factory and will not implement all the required
 * functionality
 *
 * All sites implmenetations should go in the library/Search/Site directory
 */
class Search_Parser_Site {

    /**
     * This function should return the host this site works with. This is used
     * in the Search_Parser_Factory class to register the classes correctly.
     *
     * If either it isn't a proper site class, or the class shouldn't be
     * registered, either do not define the function in the sub class or return
     * null
     *
     * @return string The host that this site supports or null if it doesn't
     */
    public static function getHost() {
        return null;
    }

    /**
     * Checks if the site needs to be logged into before data can be retrived.
     *
     * If this is true, for every page that is loaded, isLoggedIn is called
     */
    protected function needsLogin(Search_Parser_Page $p) {
        return false;
    }

    /**
     * If this returns true, then login is called and the current page is re
     * downloaded
     *
     * @param Search_Parser_Page $p
     * @return <type>
     */
    protected function isLoggedIn(Search_Parser_Page $p) {
        return $p->isLoggedIn();
    }

    /**
     * Called before the webpage is requested
     * @param InternetGateway $ig
     */
    protected function login(Search_HTTP_Client $ig) {
    }

    /**
     * Called after the webpage is connected
     * @param InternetGateway $ig
     *
     * @deprecated
     */
    protected function logout(Search_HTTP_Client $ig) {
    }

    /**
     *
     * @todo need better name
     * @return Search_Parser_Page
     */
    private function getPageImp(Search_HTTP_Client $i, $cls, URL $url, $cache = true) {
        $result = $i->getWebpage($url, 'GET', $cache);
        //echo $result;

        if ( $result->getStatus() != 200 ) {
            throw new Exception("Site status must be 200");
        }

        $body = new Search_Parser_Dom($result->getBody());
        $obj = new $cls($url, $body);
        //$body->clear();
        //unset($body);
        return $obj;

    }

    /**
     * Gets the page object for the given site
     *
     * @param URL $url
     * @return Page
     */
    public function getPage(URL $url) {
        $cls = $this->getPageClass();
        assert(class_exists($cls));

        $i = new Search_HTTP_Client();

        $obj = $this->getPageImp($i, $cls, $url);

        if ( $this->needsLogin($obj) && !$this->isLoggedIn($obj) ) {
            $this->login($i);
            $obj = $this->getPageImp($i, $cls, $url, false);

            if ( !$this->isLoggedIn($obj) ) {
                throw new Exception('Failed to log in');
            }
        }
        $obj->parsePage();

        return $obj;
    }

    public function getPageClass() {
        return get_class($this)."_page";
    }

    public function getUpdatePage() {
        return null;
    }
    public function getLimitBytes() {
        throw new Exception("Function not implemented");
    }
    public function getInitialPages() {
        return array();
    }
}