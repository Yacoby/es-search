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

class HTTPException extends Exception {

}

/**
 * This class should be used for accessing webpages so features such as caching
 * can be used.
 *
 * The class is a Facade pattern based wrapper arround the zend http client class,
 * also including the limits class, caching and automatic setting of the user agent.
 *
 * This has a 24 hour page cache time when testing
 *
 * This class is also now a wrapper arround the Search_Table_CookieJar so that
 * cookies for a domain are persistant accross all requests, mimicing a real
 * browser better
 *
 * @todo this class could do with a bit of cleaning up and documentation. The cahce
 *      stuff is really messy. It is simpy to speed up testing
 */
class Search_HTTP_Client {
    /**
     * @var Zend_Http_Client
     */
    private $_client;
    /**
     * @var Search_HTTP_Limits
     */
    private $_limits;

    /**
     *
     * @var Search_Table_CookieJar
     */
    private $_jar;

    private $_cacheEnabled = true;
    private $_cacheTime;

    /**
     * @return A value for the user agent that may depend on if the application
     *      is in testing.
     */
    private function getUserAgent() {
        if ( APPLICATION_ENV == 'testing' || APPLICATION_ENV == 'development' ) {
            return "Mozilla/5.0 (X11; U; Linux i686; en-GB; rv:1.9.0.5) \
                   Gecko/2008121621 Ubuntu/8.04 (hardy) Firefox/3.0.6";
        }else {
            return "ES Search Bot (search.yacoby.net) Contact Yacoby on \
                Bethesda Forum if usage to high. Please don't block";
        }

    }

    /**
     *
     * @param Zend_Http_Client $client
     *      If not null, this is the client that is used. It is used as it is
     *      so the user agent is NOT SET and SHOULD BE SET
     *
     * @param Search_HTTP_Limits $limits
     *      If not null, this is the limits class that is used
     */
    public function __construct(
            Zend_Http_Client $client = null,
            Search_HTTP_Limits $limits = null,
            Search_Table_CookieJar $jar = null) {

        if ( $limits ) {
            $this->_limits = $limits;
        }else {
            $this->_limits = new Search_HTTP_Limits();
        }

        if ( $client ) {
            $this->_client = $client;
        }else {
            $this->_client = new Zend_Http_Client();
            $this->_client->setCookieJar();


            $this->_client->setConfig(
                    array(
                    'maxredirects' => 0,
                    'timeout'     => 30,
                    'useragent' => $this->getUserAgent()
            ));
        }

        if ( !$jar ) {
            $jar = new Search_Table_CookieJar();
        }
        $this->_jar = $jar;

        $this->_cacheTime = APPLICATION_ENV == 'testing' ? 24 : 0;

    }

    /**
     * @return Zend_Cache_Core
     */
    private function getCacheInstance() {
        $frontendOptions = array(
                'lifetime' => 3600*$this->_cacheTime,
                'automatic_serialization' => true,
        );

        $backendOptions = array(
                'cache_dir' => APPLICATION_PATH.'/../cache/site/'
        );

        // getting a  object
        return Zend_Cache::factory(
                'Core',
                'File',
                $frontendOptions,
                $backendOptions
        );
    }

    public function _disableCache() {
        $this->_cacheEnabled = false;
    }

    /**
     * @todo clean up
     * @param URL $url
     * @return Zend_Http_Response
     */
    public function getWebpage(URL $url, $method = 'GET', $cache = true) {
        $ic = $this->getCacheInstance();
        $cid = md5($url->toString());

        if ( $this->_cacheTime > 0 &&
                $this->_cacheEnabled &&
                $ic->test($cid) &&
                $cache == true) {
            $this->_client->resetParameters();
            return $ic->load($cid);
        }else {

            $domain = $url->getHost();

            foreach ( $this->_jar->getCookies($domain) as $cookie ){
                $this->_client->getCookieJar()->addCookie($cookie);
            }

            $this->_client->setUri($url->toString());
            $req = $this->_client->request($method);

            if ( !$req->isSuccessful() ) {
                throw new HTTPException(
                "Invalid return status (". $req->getStatus() . ") when requesting $url"
                );
            }else if ( $req->isRedirect() ) {
                throw new HTTPException(
                "Redirects not supported, but were encountered when retriving $url"
                );
            }

            $this->_limits->addRequesedPage($url, strlen($req->getBody()));

            $this->_jar->addOrUpdateCookies(
                    $this->_client->getCookieJar()->getAllCookies(Zend_Http_CookieJar::COOKIE_OBJECT)
            );

            if ( $this->_cacheTime > 0 && $cache ) {
                $ic->save($req, $cid);
                assert($ic->test($cid));
            }

            $this->_client->resetParameters();
            return $req;
        }
    }

    public function addPostParameter($k, $v) {
        $this->_client->setParameterPost($k, $v);
    }
    public function setHeader($h, $v) {
        $this->_client->setHeaders($h, $v);
    }

    /**
     * Checks if it is possible to get a url without exceeding the limits even more
     * It is simply a check based on the host in the url.
     *
     * @param URL $url
     * @return bool
     */
    public function canGetWebpage($url) {
        assert($this->_limits->hasLimits($url));
        return $this->_limits->canGetPage($url);
    }

}
