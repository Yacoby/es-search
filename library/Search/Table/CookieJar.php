<?php
class Search_Table_CookieJar extends Search_Table_Abstract implements Search_HTTP_CookieJar_Interface {
    public function __construct($conn = null) {
        parent::__construct('CookieJar',$conn);
    }

    public function addOrUpdateCookies(array $cookiePacket, $domain) {
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
        $row = $this->findOneByDomain($domain);
        return $row ? unserialize($row->data) : array();
    }

}