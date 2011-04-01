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

final class ShsForumsPage extends Search_Parser_Site_Page {

    protected function doIsValidModPage($url) {
        $pages = array(
            'http://www\.shsforums\.net/index\.php\?autocom=downloads&showfile=\d+',
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doIsValidPage($url) {
        $pages = array(
            'http://www\.shsforums\.net/index\\.php\?autocom=downloads&showcat=\d+',
            'http://www\.shsforums\.net/index\\.php\?automodule=downloads&showcat=\d+',
            'http://www\.shsforums\.net/index\\.php\?autocom=downloads&showcat=\d+&sort_by=ASC&sort_key=file_name&num=\d+&st=\d+',
        );
        return $this->isAnyMatch($pages, $url);
    }

    public function preAddLink(Search_Url $url) {
        return new Search_Url(preg_replace('/s=[0-9a-zA-Z]*&/i', '', $url->toString()));
    }



    function getGame() {
        return "OB";
    }

    function getName() {
        $elems = $this->_html->find("td.nopad");
        foreach ($elems as $e){
            if ( isset($e->width) ){
                if ( $e->width == "100%" ){
                    return $e->plaintext;
                }
            }
        }
        return null;
    }

    function getAuthor() {
        $elem = $this->_html->find(".pformright a", 0);
        if ( $elem->parent()->prev_sibling()->plaintext == "File Name" ){
            return $elem->plaintext;
        }
        return "";
    }
    function getDescription() {
        $elems = $this->_html->find(".divpad");
        if ( !count($elems) ){
            return null;
        }
        return $elems[0]->plaintext;
    }
    function getCategory() {
        $elems = $this->_html->find("div[id=navstrip] a");
        return $elems[count($elems)-1]->plaintext;
    }

}

