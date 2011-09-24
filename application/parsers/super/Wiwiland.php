<?php

abstract class Super_Wiwland_page extends Search_Parser_Site_Page {
    protected $_sub;

    private $_html;

    function __construct($response, $sub) {
        parent::__construct($response);
        $this->_html = $response->simpleHtmlDom();
        $this->_sub = $sub;
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
     * Decodes a string from html with utf-8 entities to a utf-8 string
     *
     * @param string $str html input
     * @return string an latin-1 string
     */
    private function decode($str) {
        //$str = str_replace('&#8217;', '\'', $str); //doesn't covert this
        $str = str_replace('&nbsp;', ' ', $str); //and nbsp != sp
        $str = iconv('ISO-8859-1', 'UTF-8//TRANSLIT//IGNORE', $str);
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

        return new Search_Unicode($str);
    }

    abstract function getGame();

    function getName() {
        $r = $this->_html->find(".entry-title", 0);
        if ( isset($r->plaintext) ) {
            $str = $this->decode($r->plaintext);
            $str->trim();
            return $str;
        }
        return null;
    }

    function getAuthor() {
        $r = $this->_html->find(".soustitre", 0);
        if ( !isset($r->plaintext) ) {
            return null;
        }

        $r = trim($r->plaintext);
        if ( stripos($r, 'par') === 0 ) {
            $r = substr($r, strlen('par'));
        }
        $r = $this->decode($r);
        $r->trim();
        return $r;
    }

    function getDescription() {
        $r = $this->_html->find("div[class=texte entry-content]", 0);
        if ( !isset($r->plaintext) ) {
            return null;
        }
        return $this->decode($r->innertext);
    }
}


