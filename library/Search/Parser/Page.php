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
 * Class represets a single page. It should only be constructed by the Site class
 *
 * The class should be subclassed for each possible type of mod page it has to parse
 * it allows every element of the parsing to be redesigned to accomidate special cases.
 *
 * @todo checking if links are valid shouldn't really be in here as it requires
 * construction of a class.
 */
class Search_Parser_Page {
    /**
     * @var array
     */
    protected $_mods = array();

    /**
     * @var array
     */
    protected $_links = array();

    /**
     * @var URL
     */
    protected $_url;
    /**
     * @var Search_Parser_Dom
     */
    protected $_html = null;

    protected $_isLoggedIn = false;

    /**
     * @param URL $url
     * @param Search_Parser_Dom $html
     */
    public function __construct(URL $url, Search_Parser_Dom $html) {
        $this->_url = $url;
        $this->_html = $html;

        assert($url->isValid());

        //The call to parse the page was here, however it was removed as
        //it throws if it fails, so we can't check if it is in logged in or not

        $this->_isLoggedIn = $this->getLoginStateFromHTML();
        
    }

    /**
     * PHP sucks, as its garbage collector is refrence based it doesn't clear html
     * dom objects. This is basically something I shouldn't have to do
     */
    public function  __destruct() {
        if ( $this->_html ) {
            $this->_html->clear();
            unset($this->_html);
        }
    }

    /**
     *
     * @return array
     */
    public function links() {
        return $this->_links;
    }
    public function mods() {
        return $this->_mods;
    }

    /**
     * Returns a mod at a set index
     * 
     * @param int $index
     * @return array
     */
    public function mod($index) {
        assert(array_key_exists($index, $this->_mods));
        return $this->_mods[$index];
    }

    /**
     * Gets the logged in status from the html page. If the site requries being
     * logged in, the site should overrite this function
     *
     * @return bool
     */
    protected function getLoginStateFromHTML() {
        return false;
    }

    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }

    /**
     * @return URL The url specified by the page
     */
    public function getURL() {
        return $this->_url;
    }

    public function parsePage() {
        if ( !$this->_html ) {
            return false;
        }

        if ( $this->isValidModPage() ) {
            $this->doParseModPage();
        }
        $this->getPageLinks();

        return true;
    }

    /**
     * Gets all links in _html and adds them (if they are valid) to _links
     */
    protected function getPageLinks() {
        foreach( $this->_html->find('a') as $a ) {
            $url = new URL(html_entity_decode($a->href), $this->_url);
            $url = $this->stripFromLinks($url);

            if ( !$url->isValid() ) {
                continue;
            }
            if ( $url->toString() == $this->_url->toString() ) {
                continue;
            }


            if ( $this->isValidModPage($url) || $this->isValidPage($url) ) {
                $this->_links[] = ($url);
            }
        }
    }

    /**
     * This is called from getPageLinks. It can be overridden to allow the url
     * to be modified before being considered for searching
     *
     * @param URL $url
     * @return URL the new url
     */
    public function stripFromLinks(URL $url) {
        return $url;
    }

    /**
     * Matches a set of pcre regex excluding the start and end chars (%)
     *
     * @param array $pages
     * @param URL $url
     * @return bool
     */
    protected function isAnyMatch(array $pages, URL $url) {
        foreach ( $pages as $p) {
            if ( preg_match('%^'.$p.'$%', $url) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Classses inheriting from this class should override this function with
     * a function for parsing the page.
     */
    protected function doParseModPage() {
        throw new Exception('Fucntion not implemented');
    }

    /**
     * Function designed to make the site class implementations clearer
     * by allowing parsing diffrent things to be split up into diffrent functions
     */
    protected function useModParseHelper() {
        $prop = array(
                "Game",
                "Name",
                "Author",
                "Description",
                "Category",
                "Version"
        );

        /*
         * Checks for every method contained in the above array and if possible
         * calls it
        */
        $mod = array();
        foreach ( $prop as $p) {
            $method = "get".$p;
            if ( method_exists(get_class($this), $method) ) {
                $result = $this->{$method}();
                if ( $result === null ) {
                    throw new Exception(
                    "Failed to parse: {$p} when parsing {$this->_url}"
                    );
                }
                $mod[$p] = trim($result);
            }
        }

        $this->_mods[] = $mod;
    }

    /**
     * Checks if the given url is a valid mod page. If the url is not given or
     * is null it checks if the current page is a valid mod page.
     *
     * This function can be called as a static function, but if it is, the
     * argument must not be null
     *
     * @param URL $url
     * @return boolean
     */
    public function isValidModPage($url = null) {
        if ( $url === null ) {
            assert($this->_url instanceof URL);
            return $this->doIsValidModPage($this->_url);
        }
        assert($url instanceof URL);
        return $this->doIsValidModPage($url);
    }

    protected function doIsValidModPage($url) {
        throw new Exception('Fucntion not implemented');
    }

    /**
     * Is the mod page valid for searching for mods
     *
     * @param URL $url
     * @return boolean
     */
    public function isValidPage($url = null) {
        if ( $url === null ) {
            assert($this->_url instanceof URL);
            return $this->doIsValidPage($this->_url);
        }
        assert($url instanceof URL);
        return $this->doIsValidPage($url);
    }

    /**
     * Classses inheriting from this class should override this function with
     * a function for checking if the url is valid
     *
     * @param URL $url
     * @return bool true if the $url is a valid page
     */
    protected function doIsValidPage($url) {
        throw new Exception('Fucntion not implemented');
    }

    /**
     * Stipps all none ascii chars from a string
     *
     * @param string $str the string to strip
     * @return an ascii string
     *
     * @todo This is public due to it needing to be unittestable. Consider
     *      moving this to a utill class
     */
    public static function _stripNonAscii($str) {
        return preg_replace('/[^(\x20-\x7E)\x0A\x0D]*/','', $str);
    }

    /**
     * Helper function. Converts html string into plain text string converting
     * line breaks to \n
     *
     * All functions that return more than a single line of html should run the
     * html through here
     *
     * @param $str the string of raw HTML
     * @return html with the tags stripped
     *
     */
    public static function getDescriptionText($str) {
        //should match and <BR> <br /> etc
        $str = preg_replace('%<br[\\s]*[/]??>%is', "\n", $str);

        //strip everthing else
        return strip_tags($str);
    }

}
