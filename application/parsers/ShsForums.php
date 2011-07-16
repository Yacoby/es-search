<?php

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

