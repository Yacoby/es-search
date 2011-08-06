<?php

/**
 * This is a helper class for parsing simple mod sites and make some
 * of the implementation slightly nicer. Or something
 */
class Search_Parser_Site extends Search_Parser_AbstractScraper {

    /**
     * Entry pont
     */
    public function scrape(){
        return $this->getPage();
    }

    public function getPage(Search_Url $url, $client = null) {
        $cls = $this->getOption('pageClass');
        if ( !class_exists($cls, false) ){
            require_once $this->getOption('pageLocation');
        }
        assert(class_exists($cls, false));

        $i = $client ? $client : new Search_Parser_HttpClient();

        $response = $i->request($url)
                      ->method('GET')
                      ->exec();

        $obj = new $cls($response);

        if ( $obj->isModNotFoundPage($client) ) {
            throw new Search_Parser_Exception_ModRemoved('The mod was not found');
        }else if ( !$obj->isValidPageBody($obj) ) {
            throw new Search_Parser_Exception_InvalidPage(
            "The mod page at {$url} was found to be invalid"
            );
        }

        if ( !$obj->isLoggedIn() ) {
            $obj->login($i);

            //we found an invalid page, we need to delete it from the cache
            //so we don't need to get it again. This is mainly for helping
            //testing.
            $response->removeFromCache();

            $response = $i->request($url)
                          ->method('GET')
                          ->exec();
            $obj = new $cls($response);

            if ( !$obj->isLoggedIn() ) {
                throw new Search_Parser_Exception_Login(
                "Failed to log in when requesting {$url}"
                );
            }
        }
        $obj->parsePage($i);
        return $obj;
    }


    public function getLimitBytes() {
        if (!$this->hasOption('limitBytes')){
            throw new Exception($this->getOption('host') . " has no property limitBytes");
        }
        return $this->getOption('limitBytes');
    }
    /**
     * This function returns the host this site works with. This is used
     * in the Search_Parser_Factory class to register the classes correctly.
     *
     * @return string The host that this site supports or null if it doesn't
     */
    public function getHost() {
        return $this->getOption('host');
    }
    /**
     * The domain of the site. This is usally constructed by just prefixing
     * the host by http://
     *
     * @return string
     */
    public function getDomain() {
        if ( !$this->hasOption('domain') || $this->getOption('domain') == null ){
            $host = $this->getHost();
            if ( $host == null ) {
                throw new Exception('Host was null so couldn\'t compute domain');
            }
            return "http://{$host}";
        }
        return $this->getOption('domain');
    }

    /**
     * This should be overridden by inheriting classes to return the GET prefix
     * common to all mods. The return value should be constant. This doesn't
     * refer to the pages that the mods are parsed from but the pages that the
     * user is directed to (The actual mod location)
     *
     * @return string
     */
    public function getModUrlPrefix() {
        if ( !$this->hasOption('modUrlPrefix') ){
            throw new Exception("{$this->getHost()} should have a url prefix");
        }
        return $this->getOption('modUrlPrefix');
    }

    /**
     * This returns the frequency that the update pages are parsed in days. There
     * is no guaretee that it will be parsed exactly in this time periord, but it
     * should be very close. (Minutes rather than hours)
     *
     * @return int|float
     */
    public function getUpdateFrequency() {
        return $this->getOption('updateFrequency');
    }
    /**
     * Gets the pages that should be checked every now and again that lists the
     * updated mods. This is guarenteed to be parsed about once every UpdateFrequency
     * so doesn't have to refer to an update page as such. This shouldn't be overwridden
     * but the values returned in this should be defined in _getUpdateDetails()
     *
     * @return array
     */
    public function getUpdatePages() {
        if ( !$this->hasOption('updateUrl') ){
            return array();
        }
        return $this->convertUrlSuffixes($this->getOption('updateUrl'));
    }

    /**
     * Gets the pages that should be used as a seed for finding the mods that
     * have already been added so won't be found in the update pages.
     *
     * These could be parsed more than once, and the current implementation re-adds
     * them every month or so.
     *
     * @return array
     */
    public function getInitialPages() {
        if ( !$this->hasOption('initialPages') ){
            return array();
        }
        return $this->convertUrlSuffixes($this->getOption('initialPages'));
    }
    /**
     * Takes an array of url suffixes, merges them with the domain, wraps them
     * in a Search_Url and returns the new array
     *
     * @param array $suffixes
     * @return array
     */
    private function convertUrlSuffixes(array $suffixes) {
        $urls = array();
        foreach ( $suffixes as $urlSuffix ) {
            $urls[] = new Search_Url($this->getDomain().$urlSuffix);
        }
        return $urls;
    }


    protected function needsLogin(Search_Parser_Page $p) {
        return $this->getOption('loginRequired');
    }



}
