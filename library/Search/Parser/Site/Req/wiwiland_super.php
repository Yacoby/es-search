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

abstract class Wiwland_Super extends Search_Parser_Site {
    private $_sub;
    function __construct($sub) {
        $this->_sub = $sub;
    }
    /**
     * Maximum Usage Per day
     */
    public function getLimitBytes() {
        return 1048578;
    }

    /**
     * Gets the page used to update the mod.
     */
    public function getUpdatePage() {
        $up = array(
                new URL("http://".$this->_sub.".wiwiland.net/spip.php?page=classemois"),
        );

        return array(
                "URL" => $up,
                "UpdateF" => 31
        );
    }


    public function getInitialPages() {
        return array();
    }
}

abstract class Super_Wiwland_page extends Search_Parser_Page {
    protected $_sub;

    function __construct(URL $url, Search_Parser_Dom $html, $sub) {
        parent::__construct($url,$html);
        $this->_sub = $sub;
    }

    protected function doIsValidModPage($url) {
        $pages = array(
                //"http://".$this->_sub."\\.wiwiland\\.net/spip\\.php\\?article\\d+",
                "http://morromods\\.wiwiland\\.net/spip\\.php\\?article\\d+",
                "http://oblimods\\.wiwiland\\.net/spip\\.php\\?article\\d+",
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doIsValidPage($url) {
        return false;
    }

    protected function doParseModPage() {
        return $this->useModParseHelper();
    }

    /**
     * Decodes a string from html with utf-8 entities to a latin-1 string
     *
     *
     * @param string $str html input
     * @return string an ascii string
     */
    private function decode($str) {

        $str = str_replace('&#8217;', '\'', $str); //doesn't covert this
        $str = str_replace('&nbsp;', ' ', $str); //and nbsp != sp

        return iconv(
                'ISO-8859-1',
                'ASCII//TRANSLIT//IGNORE',
                //utf8_decode(
                html_entity_decode($str, ENT_QUOTES, 'UTF-8')
                //        )
        );
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
        //strip the Par from the front of the string
        //$r = $r->plaintext;
        $r = $this->decode($r->plaintext);//html_entity_decode($r, ENT_QUOTES, 'UTF-8');
        $r = trim(substr($r, strlen('Par')));
        return $r;
    }
    function getDescription() {
        $r = $this->_html->find("div[class=texte entry-content]", 0);
        if ( !isset($r->plaintext) ) {
            return null;
        }
        return self::getDescriptionText($this->decode($r->innertext));
    }
    /*
    function getVersion() {
        $r = $this->_html->find(".surtitre", 0);
        if ( !isset($r->plaintext) ) {
            return '';
        }
        //it someimtes says v1.5
        $r = $r->plaintext;
        $r = ltrim($r, 'Vv');
        return $r;
    }
    */
}


