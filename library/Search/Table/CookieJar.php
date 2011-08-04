<?php
class Search_Table_CookieJar extends Search_Table_Abstract implements Search_HTTP_CookieJar_Interface {
    public function __construct($conn = null) {
        parent::__construct('CookieJar',$conn);
    }

    private $_cache = array();

    public function addOrUpdateCookies(array $cookiePacket, $domain) {
        $this->_cache[$domain] = $cookiePacket;
        foreach ( $cookiePacket as $key => $cookie ) {
            if ( $cookie->isExpired() ) {
                unset($cookiePacket[$key]);
            }
        }

        $cookies         = $this->create();
        $cookies->domain = $domain;
        $cookies->data   = serialize($cookiePacket);
        $cookies->replace();
    }
    /**
     * @todo Not very efficiant
     * 
     * @param string $domain
     * @return array
     */
    public function getCookies($domain) {
        if ( array_key_exist($domain, $this->_cache) ){
            return $this->_cache[$domain];
        }
        $row = $this->findOneByDomain($domain);
        $this->_cache[$domain] = $row ? unserialize($row->data) : array();
        return $this->_cache[$domain]; 
    }

}
