<?php

/**
 * This class is responsible for haneling the request for a page
 *
 * It shoudln't be constructed, except through the Search_HTTP_Client class
 *
 * It is single use. It cannot be used for multiple requests.
 */
class HttpRequestObject{
    private $_url;
    private $_client;

    private $_parent;

    /**
     * The request method. It must be either GET or POST
     *
     * @var string
     */
    private $_method = 'GET';

    /**
     * True if the cache is enabled
     *
     * @var bool
     */
    private $_cacheEnabled = true;

    /**
     * True if the request will be saved (assuming the cache enabled).
     * If the cache is disabled, this has no effect
     * @var bool
     */
    private $_cacheSaveEnabled = true;

    /**
     * This is designed as a single use object. Once the exec funciton has
     * been called it shouldn't be reused. This will be false after exec
     * has been called
     *
     * @var bool
     */
    private $_validObject = true;

    private $_cacheInstance;

    private $_jar;

    public function __construct($parent, $client, $jar, $cache, $cacheTime){
        $this->_parent        = $parent;
        $this->_client        = $client;
        $this->_jar           = $jar;
        $this->_cacheTime     = $cacheTime;
        $this->_cacheInstance = $cache;
    }
    /**
     *
     * @param Search_Url $url
     * @return HttpRequestObject
     */
    public function url(Search_Url $url){
        $this->_url = $url;
        return $this;
    }
    /**
     * Sets the request method
     *
     * @param string $method
     * @return HttpRequestObject
     */
    public function method($method){
        if ( !$method == 'POST' && !$method == 'GET' ){
                throw new HTTPException('Method must be GET or POST');
        }
        $this->_method = $method;
        return $this;
    }
    /**
     * Sets weather or not to use the cache. This can be useful in cases such as
     * when loading login pages et al
     *
     * @return HttpRequestObject
     */
    public function withCache($val){
        assert ( (bool)$val == $val );
        $this->_cacheEnabled = $val;
        return $this;
    }
    /**
     * This is by default true. If set to false, the page will not be saved,
     * however the page may still be loaded from the cache
     *
     * If the cache is not enabled, then changing this has no effect
     *
     * @param  bool $val
     * @return HttpRequestObject
     */
    public function cacheOutput($val){
        assert ( (bool)$val == $val );
        $this->_cacheEnabled = $val;
        return $this;
    }
    /**
     *
     * @param string|array $k the key of the paramter
     * @param string $v
     * @return HttpRequestObject
     */
    public function addPostParameter($k, $v) {
        $this->_client->setParameterPost($k, $v);
        return $this;
    }
    /**
     *
     * @param <type> $h
     * @param <type> $v
     * @return HttpRequestObject
     */
    public function setHeader($h, $v) {
        $this->_client->setHeaders($h, $v);
        return $this;
    }

    /*
     * Processes the request and returns the response
     *
     * @return Zend_Http_Response
     */
    public function exec(){
        //@todo maybe more than a assert?
        assert ( $this->_validObject );
        $this->_validObject = false;

        //The cache ID. This is hopefully fairly unqiue...
        $cid = md5($this->_url->toString());

        //test the cache to see if the page is already there
        if ( $this->_cacheTime > 0 && $this->_cacheEnabled &&
            $this->_cacheInstance->test($cid) ) {
            return $this->_cacheInstance->load($cid);
        }else {
            $this->setRequestCookies();

            //get the page
            $this->_client->setUri($this->_url->toString());
            $req = $this->_client->request($this->_method);

            //used for limits if required
            $this->_parent->event()->onPageRequst($req);

            if ( $req->isRedirect() ) {
                throw new Search_HTTP_Exception_Redirect(
                    $req->getHeader('Location'),
                    "Redirects not supported, but were encountered when retriving $this->_url"
                );
            }else if ( !$req->isSuccessful() ) {
                throw new HTTPException(
                    "Invalid return status (". $req->getStatus() . ") when requesting {$this->_url}"
                );
            } 

            $this->updateDbCookies();

            //save the page for future use
            if ( $this->_cacheSaveEnabled && $this->_cacheEnabled ) {
                $this->_cacheInstance->save($req, $cid);
                //TODO WHY SOMETIMES FAIL?
                //assert($this->_cacheInstance->test($cid));
            }

            return $req;
        }
    }

    /**
     * Puts the cookies from the database in the cookie jar
     */
    private function setRequestCookies(){
        $domain = $this->_url->getHost();

        //copy all cookies from the db to the jar
        $this->_client->getCookieJar()->reset();
        foreach ( $this->_jar->getCookies($domain) as $cookie ){
            $this->_client->getCookieJar()->addCookie($cookie);
        }
    }
    /**
     * Updates the cookies that have been set
     */
    private function updateDbCookies(){
        $domain = $this->_url->getHost();
        $this->_jar->addOrUpdateCookies(
            $this->_client->getCookieJar()
                          ->getAllCookies(Zend_Http_CookieJar::COOKIE_OBJECT),
            $domain
        );
    }
}
class HTTPException extends Exception {}
class Search_HTTP_Exception_Redirect extends Exception {
    private $_to;
    public function  __construct($to, $message = "", $code = 0 , $previous = NULL) {
        parent::__construct($message, $code, $previous);
        $this->_to = $to;
    }
    public function to(){
        return $this->_to;
    }
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
 */
class Search_HTTP_Client extends Search_Observable{
    /**
     * @var Zend_Http_Client
     */
    private $_client;

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
     *
     *  @todo Should we really do this? If we have a cache, I don't think
     *          it really matters
     */
    private function getUserAgent() {
        if ( APPLICATION_ENV == 'testing' || APPLICATION_ENV == 'development' ) {
            return "Mozilla/5.0 (X11; U; Linux i686; en-GB; rv:1.9.0.5) "
                 . "Gecko/2008121621 Ubuntu/8.04 (hardy) Firefox/3.0.6";
        }else {
            return "ES Search Bot (search.yacoby.net) Contact Yacoby on "
                 . "Bethesda Forum if usage to high or email Search@JacobEssex.com";
        }
    }

    /**
     *
     * @param Zend_Http_Client $client
     *      If not null, this is the client that is used. It is used as it is
     *      so the user agent is NOT SET and SHOULD BE SET
     *
     * @param Search_Table_CookieJar $jar
     *      If not null, this is the class that allows access to the cookies
     *
     */
    public function __construct(
            Zend_Http_Client $client             = null,
            Search_HTTP_CookieJar_Interface $jar = null
    ) {
        $this->_jar       = $jar ? $jar : new Search_Table_CookieJar();
        $this->_cacheTime = APPLICATION_ENV == 'testing' ? 24 : 0;

        /*
         The client can't be set quite as simply as the above as some
         configuaration options need to be set
         */
        if ( $client ) {
            $this->_client = $client;
        }else {
            $this->_client = new Zend_Http_Client();
            $this->_client->setCookieJar();

            $this->_client->setConfig(
                array(
                    'maxredirects'  => 1,
                    'timeout'       => 30,
                    'useragent'     => $this->getUserAgent()
                )
            );
        }

    }

    public function event(){
        return parent::event();
    }

    /**
     * @return Zend_Cache_Core
     */
    private function getCacheInstance() {
        $frontendOptions = array(
                'lifetime'                  => 3600*$this->_cacheTime,
                'automatic_serialization'   => true,
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

    /**
     * @todo teh fact that this is needed indicats a design flaw
     */
    public function _disableCache() {
        $this->_cacheEnabled = false;
    }

    public function request(Search_Url $url){
        $ro = new HttpRequestObject($this, 
                                    $this->_client,
                                    $this->_jar,
                                    $this->getCacheInstance(),
                                    $this->_cacheTime);
        return $ro->url($url);
    }
}
