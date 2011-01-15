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
 * Superclass for a site, all sites should inherit from this class, else they
 * will not get picked up by the factory and will not implement all the required
 * functionality
 *
 * All sites implmenetations should go in the library/Search/Site directory
 */
class Search_Parser_Site {
    /**
     * This contains all the settings for the site.
     *
     * @var array
     */
    protected $_details = array(
        /*
         * This is the host that this site works with.
         * If either it isn't a proper site class, or the class shouldn't be
         * registered, set this to null
         */
        'host'            => null,

        /*
         * If the domain differs from standard in some way, e.g. using https this
         * should be overwridden and return a constant string with the correct domain
         * otherwise leave this as null. It will be automatically constructed
         * from the host by adding http:// to the host.
         */
        'domain'          => null,

        /*
         * This is the GET prefix common to all mods. This doesn't  refer to
         * the pages that the mods are parsed from but the pages that the
         * user is directed to (The actual mod location)
         */
        'modUrlPrefix'    => '',

        /*
         * This should be an array of all the suffixes of the seed pages to parse
         * If there are no seed pages it should return an empty array
         */
        'initialPages'    => array(),

        /*
         * Gets the pages that should be checked every now and again that lists the
         * updated mods. This is guarenteed to be parsed about once every UpdateFrequency
         * so doesn't have to refer to an update page as such.
         */
        'updateUrl'       => array(),

        /*
         * This is the frequency that the update pages are parsed in days. There
         * is no guaretee that it will be parsed exactly in this time periord, but it
         * should be very close. (Minutes rather than hours)
         */
        'updateFrequency' => 0,

        /*
         * True if the site needs to be logged into before data can be retrived.
         */
        'loginRequired'   => false,

        /*
         * The maximum number of bytes that can be retrived from the site
         * each day
         */
        'limitBytes'      => 0,
    );

    /**
     * This function returns the host this site works with. This is used
     * in the Search_Parser_Factory class to register the classes correctly.
     *
     * @return string The host that this site supports or null if it doesn't
     */
    public function getHost() {
        return $this->_details['host'];
    }
	/**
	 * The domain of the site. This is usally constructed by just prefixing
     * the host by http://
     *
	 * @return string
	 */
	public function getDomain(){
        if ( $this->_details['host'] === null ){
            $host = $this->getHost();
            if ( $host === null ){
                throw new Exception('Host was null so couldn\'t compute domain');
            }
            return "http://{$host}";
        }
        return $this->_details['host'];
	}
    
	/**
	 * This should be overridden by inheriting classes to return the GET prefix
	 * common to all mods. The return value should be constant. This doesn't
	 * refer to the pages that the mods are parsed from but the pages that the
     * user is directed to (The actual mod location)
	 *
	 * @return string
	 */
	public static function getModUrlPrefix(){
        return $this->_details['modUrlPrefix'];
	}

	/**
	 * This returns the frequency that the update pages are parsed in days. There
	 * is no guaretee that it will be parsed exactly in this time periord, but it
	 * should be very close. (Minutes rather than hours)
	 *
	 * @return int|float
	 */
	public function getUpdateFrequency(){
        return $this->_details['updateFrequency'];
	}
	/**
	 * Gets the pages that should be checked every now and again that lists the
	 * updated mods. This is guarenteed to be parsed about once every UpdateFrequency
	 * so doesn't have to refer to an update page as such. This shouldn't be overwridden
	 * but the values returned in this should be defined in _getUpdateDetails()
	 *
	 * @return array
	 */
	public function getUpdatePages(){
        return $this->convertUrlSuffixes($this->_details['updatePages']);
		//$up = $this->_getUpdateDetails();
		//return $this->convertUrlSuffixes($up['Urls']);
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
	public function getInitialPages(){
        return $this->convertUrlSuffixes($this->_details['initialPages']);
		//return $this->convertUrlSuffixes($this->_getInitialPages());
	}
	/**
	 * Takes an array of url suffixes, merges them with the domain, wraps them
	 * in a Search_Url and returns the new array
	 *
	 * @param array $suffixes
	 * @return array
	 */
	private function convertUrlSuffixes(array $suffixes){
		$urls = array();
		foreach ( $suffixes as $urlSuffix ){
			$urls[] = new Search_Url($this->getDomain().$urlSuffix);
		}
		return $urls;
	}


    protected function needsLogin(Search_Parser_Page $p) {
        return $this->_details['loginRequired'];
    }

    /**
     * If this returns true, then login is called and the current page is re
     * downloaded
     *
     * @param Search_Parser_Page $p
     * @return bool
     */
    protected function isLoggedIn(Search_Parser_Page $p) {
        return $p->isLoggedIn();
    }

    /**
     * Called before the webpage is requested
     * @param Search_HTTP_Client $ig
     */
    protected function login(Search_HTTP_Client $ig) {
    }

    /**
     * Called after the webpage is connected
     * @param Search_HTTP_Client $ig
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
    private function getPageImp(Search_HTTP_Client $i, $cls, Search_Url $url, $cache = true) {
        $result = $i->request($url)
                    ->method('GET')
                    ->cacheOutput($cache)
                    ->exec();

        if ( $result->getStatus() != 200 ) {
            throw new Exception("Site status must be 200 and wasn't when requesting {$url}");
        }

        $body = new Search_Parser_Dom($result->getBody());
        $obj = new $cls($url, $body);

        return $obj;
    }

    /**
     * Gets the page object for the given site
     *
     * @param Search_Url $url
     * @return Page
     */
    public function getPage(Search_Url $url, $client = null) {
        $cls = $this->getPageClass();
        assert(class_exists($cls));

        $i = $client ? $client : new Search_HTTP_Client();

        $obj = $this->getPageImp($i, $cls, $url);

        if ( !$obj->isValidPageBody($obj) ) {
            throw new Search_Parser_Exception_InvalidPage(
            "The mod page at {$url} was found to be invalid"
            );
        }

        if ( $this->needsLogin($obj) && !$this->isLoggedIn($obj) ) {
            $this->login($i);
            $obj = $this->getPageImp($i, $cls, $url, false);

            if ( !$this->isLoggedIn($obj) ) {
                throw new Search_Parser_Exception_Login(
                "Failed to log in when requesting {$url}"
                );
            }
        }
        $obj->parsePage($i);

        return $obj;
    }

    public function getPageClass() {
        return get_class($this).'_page';
    }

    public function getLimitBytes() {
        return $this->_details['limitBytes'];
    }
}