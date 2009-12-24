<?php /* l-b
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
 * l-b */ ?>

<?php
//Search_Parser_Factory::getInstance()->register("modhistory.fliggerty.com", "modding_history");

/**
 */
final class modding_history extends Search_Parser_Site {
    
    public static function getHost(){
        return null;
        //return 'modhistory.fliggerty.com';
    }

/**
 * Maximum Usage Per day
 */
    public function getLimitBytes() {
        return 1048578*10;
    }

    /**
     * Gets the page used to update the mod.
     */
    public function getUpdatePage() {
        return array(
        "URL" => array(),
        "UpdateF" => 31
        );
    }

    public function getInitialPages() {
        return array(
            'http://modhistory.fliggerty.com/rwdownload/index.php?cid=5'
        );
    }

}

final class modding_history_page extends Search_Parser_Page {

    protected function doIsValidModPage($url) {
        return (preg_match("%http://modhistory\\.fliggerty\\.com/rwdownload/index\\.php\\?dlid=\\d+%", $url->toString()) == 1 );
    }

    protected function doIsValidPage($url) {
        $pages = array(
            "http://modhistory\\.fliggerty\\.com/rwdownload/index\\.php\\?cid=\\d+",
            "http://modhistory\\.fliggerty\\.com/rwdownload/index\\.php\\?cid=\\d+&sortvalue=date&order=ASC&limit=\\d+"
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doParseModPage() {
        return $this->useModParseHelper();
    }

    function getGame() {
        return "MW";
    }
    function getName() {
        return $this->_html->find("div[id=banner] div[id=catname] a", 0)->plaintext;
    }
    function getAuthor() {
        $mr = $this->_html->find('.mainrow');
        foreach ( $mr as $r ) {
            if ( trim($r->find('td',0)->plaintext) == "Author:" ) {
                return $r->find('td',1)->plaintext;
            }
        }
        return null;
    }

    function getDescription() {
        $mr = $this->_html->find('.mainrow');
        foreach ( $mr as $r ) {
            if ( count($r->children()) == 1) {
                return $r->children(0)->plaintext;
            }
        }
        //optional
        return " ";
    }

    function getCategory(){
        $mr = $this->_html->find('.topbg strong',0);
        if ( $mr == null )
            return ' ';
            
        $n = count($mr->children());
        return $mr->children($n-2)->plaintext;

    }


}

