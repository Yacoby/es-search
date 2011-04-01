<?php
/* l-b
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
 * l-b */


/**
 * Class represets a single page. It should only be constructed by the Site class
 *
 * The class should be subclassed for each possible type of mod page it has to parse
 * it allows every element of the parsing to be redesigned to accomidate special cases.
 *
 * @todo checking if links are valid shouldn't really be in here as it requires
 * construction of a class.
 */
class Search_Parser_Site_Page extends Search_Parser_Page_Abstract {

    /**
     * @var Search_Url
     */
    protected $_url;
    /**
     * @var Search_Parser_Dom
     */
    protected $_html = null;

    protected $_isLoggedIn = false;

    /**
     * The Search_Parser_Dom object passsed to this object becomes owned by this
     * object and shouldn't be used after it has been passesed. This is
     * due the fact that the PHP gc works by refrence counting.
     *
     * The page is not parsed in this function as if it fails it throws, which
     * means it is impossible to check if we need to login
     *
     * @param Search_Url|null $url
     * @param Search_Parser_Dom|null $html
     */
    public function __construct($url, $html) {
        $this->_url  = $url;
        $this->_html = $html;

        $this->_isLoggedIn = $html !== null ? $this->getLoginStateFromHTML() : true;
    }

    /**
     * Clears the dom objects, this means that the dom object doesn't exist after
     * this., even if it refrenced elsewhere
     *
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
     * Returns a mod at a set index
     *
     * @param int $index
     * @return array
     */
    public function mod($index) {
        $mods = $this->mods();
        assert ((int)$index == $index);
        assert(array_key_exists($index, $mods));
        return $mods[$index];
    }

    /**
     * Gets the logged in status from the html page. If the site requries being
     * logged in, the site should overwrite this function
     *
     * @todo rename to something like _getIsLoggedInFromHTML
     *
     * @return bool true if logged in
     */
    protected function getLoginStateFromHTML() {
        return false;
    }

    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }

    /**
     * @return Search_Url The url specified by the page
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * Parses the page
     *
     * @return bool sucsess
     */
    public function parsePage($client) {
        if ( !$this->_html ) {
            return false;
        }

        if ( $this->isValidModPage() ) {
            try {
                //try parsing
                $this->doParseModPage($client);

                //on a parse error, check if it is because the mod has been
                //deleted
            }catch(Search_Parser_Exception_Parse $e) {
                //if it is, change the exception
                if ( $this->isModNotFoundPage($client) ) {
                    throw new Search_Parser_Exception_ModRemoved();
                }else {
                    //otherwise, just throw the parse error upwards
                    throw $e;
                }
            }
        }
        $this->getPageLinks();

        return true;
    }

    /**
     *
     * This should return true if the current page is related to a mod not found
     * or mod no longer exists or whatever.
     */
    public function isModNotFoundPage($client) {
        return false;
    }

    /**
     * Gets all links in _html and adds them (if they are valid) to _links
     */
    protected function getPageLinks() {
        foreach( $this->_html->find('a') as $a ) {
            $url = new Search_Url(html_entity_decode($a->href), $this->_url);
            $url = $this->preAddLink($url);

            if ( !$url->isValid() || $url->toString() == $this->_url->toString() ) {
                continue;
            }

            if ( $this->isValidModPage($url) || $this->isValidPage($url) ) {
                $this->addLink($url);
            }
        }
    }

    /**
     * This is called from getPageLinks. It can be overridden to allow the url
     * to be modified before being considered for searching
     *
     * @param Search_Url $url
     * @return Search_Url the new url
     */
    public function preAddLink(Search_Url $url) {
        return $url;
    }

    /**
     * Matches a set of pcre regex excluding the start and end chars (%)
     *
     * @param array $pages
     * @param Search_Url $url
     * @return bool
     */
    protected function isAnyMatch(array $pages, Search_Url $url) {
        foreach ( $pages as $p) {
            if ( preg_match('%^'.$p.'$%', $url) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Classses inheriting from this class should override this function with
     * a function for parsing the page unless they want to use the helper
     */
    protected function doParseModPage($client) {
        return $this->useModParseHelper($client);
    }

    /**
     * Function designed to make the site class implementations clearer
     * by allowing parsing diffrent things to be split up into diffrent functions
     */
    protected function useModParseHelper($client) {
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
                $result = $this->{$method}($client);
                if ( $result === null ) {
                    throw new Search_Parser_Exception_Parse(
                    "Failed to parse {$p} when parsing {$this->_url}"
                    );
                }
                $mod[$p] = trim($result);
            }
        }

        $this->addMod($mod);
    }

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

    /**
     * Checks if the given url is a valid mod page. If the url is not given or
     * is null it checks if the current page is a valid mod page.
     *
     * @param Search_Url $url
     * @return boolean
     */
    public function isValidModPage($url = null) {
        if ( $url === null ) {
            assert($this->_url instanceof Search_Url);
            return $this->doIsValidModPage($this->_url);
        }
        assert($url instanceof Search_Url);
        return $this->doIsValidModPage($url);
    }
    /*
     * Classses inheriting from this class should override this function with
     * a function for checking if the url is valid
    */
    protected function doIsValidModPage($url) {
        throw new Search_Parser_Exception_Unimplemented('Fucntion '.__FUNCTION__.' not implemented');
    }

    /**
     * Is the mod page valid for searching for mods
     *
     * @param Search_Url $url
     * @return boolean
     */
    public function isValidPage($url = null) {
        if ( $url === null ) {
            assert($this->_url instanceof Search_Url);
            return $this->doIsValidPage($this->_url);
        }
        assert($url instanceof Search_Url);
        return $this->doIsValidPage($url);
    }

    /**
     * Classses inheriting from this class should override this function with
     * a function for checking if the url is valid
     *
     * @param Search_Url $url
     * @return bool true if the $url is a valid page
     */
    protected function doIsValidPage($url) {
        throw new Search_Parser_Exception_Unimplemented('Fucntion '.__FUNCTION__.' not implemented');
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
        $str = str_replace("\n", "", $str);

        //should match and <BR> <br /> etc
        $str = preg_replace('%<br[\\s]*[/]??>%is', "\n", $str);

        //strip everthing else
        return strip_tags($str);
    }

}
