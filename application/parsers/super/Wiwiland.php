<?php

abstract class Super_Wiwland_page extends Search_Parser_Site_Page {
    protected $_sub, $_conv = array();

    private $_html;

    function __construct($response, $sub) {
        parent::__construct($response);
        $this->_html = $response->simpleHtmlDom();
        $this->_sub = $sub;

        for( $i = 32; $i <= 255; $i++ ) {
            $this->_conv[chr($i)] = utf8_encode(chr($i));
        }
    }

    protected function doIsValidModPage($url) {
        $pages = array(
                "http://morromods\\.wiwiland\\.net/spip\\.php\\?article\\d+",
                "http://oblimods\\.wiwiland\\.net/spip\\.php\\?article\\d+",
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doIsValidPage($url) {
        return false;
    }

    /**
     * Decodes a string from html with utf-8 entities to a latin-1 string
     *
     *
     * @param string $str html input
     * @return string an latin-1 string
     */
    private function decode($str) {

        $str = str_replace('&#8217;', '\'', $str); //doesn't covert this
        $str = str_replace('&nbsp;', ' ', $str); //and nbsp != sp
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        
        foreach( $this->_conv as $key => $val ) {
            $str = str_replace($key, $val, $str);
        }
        return $str;
    }

    abstract function getGame();

    function getName() {
        $r = $this->_html->find(".entry-title", 0);
        if ( isset($r->plaintext) ) {
            return $this->decode($r->plaintext);
        }
        return null;
    }
    function getAuthor() {

        $r = $this->_html->find(".soustitre", 0);
        if ( !isset($r->plaintext) ) {
            return null;
        }

        $r = $this->decode($r->plaintext);

        if ( stripos($r, 'Par') === 0 ) {
            $r = substr($r, strlen('Par'));
        }
        return trim($r);
    }
    function getDescription() {
        $r = $this->_html->find("div[class=texte entry-content]", 0);
        if ( !isset($r->plaintext) ) {
            return null;
        }
        return self::getDescriptionText($this->decode($r->innertext));
    }
}


