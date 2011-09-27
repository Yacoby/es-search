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
class Search_Parser_Site_Page extends Search_Parser_ScrapeResult{

    protected $_url;

    public function __construct($response) {
        $this->_response = $response;
        $this->_url = $response->url();
    }
    // ------------------------------------------------------------------------
    // Commonly things that probably should be overridden
    
    /*
     * Classses inheriting from this class should override this function with
     * a function for checking if the url is valid
    */
    protected function doIsValidModPage($url) {
        throw new Search_Parser_Exception_Unimplemented('Fucntion '.__FUNCTION__.' not implemented');
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
     * This is called from getPageLinks. It can be overridden to allow the url
     * to be modified before being considered for searching
     *
     * @param Search_Url $url
     * @return Search_Url the new url
     */
    public function preAddLink(Search_Url $url) {
        return $url;
    }


    // ------------------------------------------------------------------------
    // Results data
    private $_links = array();
    public function links() {
        return $this->_links;
    }
    protected function addLink($link) {
        $this->_links[] = $link;
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


    // ------------------------------------------------------------------------
    // Response stuff
    private $_response; 
    protected function getResponse(){
        return $this->_response;
    }


    // ------------------------------------------------------------------------
    // Page validation

    /**
     * This should return true if the current page is related to a mod not found
     * or mod no longer exists or whatever.
     */
    public function isModNotFoundPage($client) {
        return false;
    }

    /**
     * Called before the webpage is requested
     * @param Search_Parser_HttpClient $ig
     */
    public function login(Search_Parser_HttpClient $ig){
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
     * Gets the logged in status from the html page. If the site requries being
     * logged in, the site should overwrite this function. If not, leave it 
     *
     * @return bool true if logged in or the site doesn't require logging in
     */
    public function isLoggedIn() {
        return true;
    }


    // ------------------------------------------------------------------------
    // Parsing stuff

    /**
     * Classses inheriting from this class should override this function with
     * a function for parsing the page unless they want to use the helper
     */
    protected function doParseModPage($client) {
        return $this->useModParseHelper($client);
    }

    /**
     * Parses the page
     *
     * @return bool sucsess
     */
    public function parsePage($client) {
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
     * Gets all links in _html and adds them (if they are valid) to _links
     */
    protected function getPageLinks() {
        $links = $this->getResponse()->html()->xpath('//a/@href');
        foreach( $links as $href ) {
            $href = $href->toString()->getAscii(); //only copes with ascii urls
            $url = new Search_Url(html_entity_decode($href), $this->_url);
            $url = $this->preAddLink($url);

            if ( !$url->isValid() || $url->toString() == $this->_url->toString() ) {
                continue;
            }

            if ( $this->isValidModPage($url) || $this->isValidPage($url) ) {
                $this->addLink($url);
            }
        }
    }


    // ------------------------------------------------------------------------
    // Utitity functions
    
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
     * @TODO this works on strings, it should only work on the unicode objects
     * howerver it isn't the biggest of issues with the functions being
     * used
     */
    public static function getDescriptionText($str) {
        $str = str_replace("\n", "", $str);

        //should match and <BR> <br /> etc
        $str = preg_replace('%<br[\\s]*[/]??>%ius', "\n", $str);

        //strip everthing else
        return strip_tags($str);
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

        $unicodeReq = array('Name', 'Author', 'Description', 'Category');

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
                if ( $result instanceof Search_Unicode ){
                    $result->trim();
                    if ( !in_array($p, $unicodeReq) ){
                        //version and game shouldn't be unicode
                        $result = $result->getAscii();
                    }
                }else{
                    if ( in_array($p, $unicodeReq) ){
                        throw new Exception('Expected unicode, didn\'t recive when trying to parse ' . $p);
                    }
                    $result = trim($result);
                }
                $mod[$p] = $result;
            }
        }

        $this->addMod($mod);
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
     * @return Search_Url The url specified by the page
     */
    public function getUrl() {
        return $this->_url;
    }

}
