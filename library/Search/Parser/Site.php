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
	 * If the domain differs from standard in some way, e.g. using http this
	 * should be overwridden and return a constant string with the correct domain
	 * otherwise this just adds a http:// to the host. As the host is constant
	 * this can be assumed to be constant.
	 *
	 * This was going to be static, be due to the lack of late static binding
	 * in PHP 5.2.x, it is not.
	 *
	 * @return string
	 */
	public function getDomain(){
		$host = $this->getHost();
		if ( $host === null ){
			throw new Exception('Host was null so couldn\'t compute domain');
		}
		return "http://{$host}";
	}
	/**
	 * This should be overridden by inheriting classes to return the GET prefix
	 * common to all mods. The return value should be constant. This doesn't
	 * refer to the pages that the mods come from but the pages that the user
	 * is directed to.
	 *
	 * @return string
	 */
	public static function getModUrlPrefix(){
		return null;
	}

	/**
	 * This returns the frequency that the update pages are parsed in days. There
	 * is no guaretee that it will be parsed exactly in this time periord, but it
	 * should be very close. (Minutes rather than hours)
	 *
	 * @return int|float
	 */
	public function getUpdateFrequency(){
		$up = $this->_getUpdateDetails();
		assert(isset($up['UpdateFrequency']));
		return $up['UpdateFrequency'];
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
		$up = $this->_getUpdateDetails();
		return $this->convertUrlSuffixes($up['Urls']);
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
		//return $this->convertUrlSuffixes($this->_getInitialPages());
		return $this->convertUrlSuffixes($this->_getInitialPages());
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


	/**
	 * This should return an array of all the suffixes of the seed pages to parse
	 * If there are no seed pages it should return an empty array
	 *
	 * @return array
	 */
	protected function _getInitialPages(){
        throw new Search_Parser_Exception_Unimplemented('Fucntion '.__FUNCTION__.' not implemented');
	}

	/**
	 * This should return a array with the following indexes. 'Urls', which should
	 * hold a list of string suffixes indicating the pages that hold mod updates.
	 * The 'UpdateFrequency' index should hold an numerical value indicating
	 * the number of days between parsing the page. 
	 * 
	 * @return array
	 */
	protected function _getUpdateDetails(){
        throw new Search_Parser_Exception_Unimplemented('Fucntion '.__FUNCTION__.' not implemented');
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
     * @return bool
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
        throw new Search_Parser_Exception_Unimplemented('Fucntion '.__FUNCTION__.' not implemented');
    }
}