<?php

/**
 * This is used for testing. It should really be in the testing classes
 * I think.
 *
 * This class stores cookies in memeory. Its main use is in testing, as
 * it allows us to test things requring cookies without having to
 * mess around with databases
 */
class Search_HTTP_CookieJar_Memory implements Search_HTTP_CookieJar_Interface{
    private $_domains;
    public function addOrUpdateCookies(array $cookies, $domain){
        $this->_domains[$domain] = $cookies;
    }
    public function getCookies($domain){
        if ( isset($this->_domains[$domain]) ){
            return $this->_domains[$domain];
        }
        return array();
    }
    private static $_instance;
    public static function getInstance(){
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c();
        }
        return self::$_instance;
    }
}