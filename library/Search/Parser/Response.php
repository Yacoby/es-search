<?php

/**
 * This is a wrapper around DOM document as using it with xpath is a bit of 
 * a pain and 90% of the time all I will be doing is throwing xpath at a dom
 * document
 *
 * This class shouldn't be initated by anything outside this file
 */
class Dom{
    private $_dom;
    private $_xpath;

    public function __construct($html = null){
        if ( $html ){
            $this->loadHtml($html);
        }
    }

    public function loadHtml($html){
        $this->_dom = new DOMDocument();
        $this->_dom->loadHTMLFile($html);

        $this->_xpath = new DOMXpath($this->_dom);
    }

    public function xpath($query){
        $elems = $this->_xpath->query($query)
        if ( $elms === false ){
            return false;
        }
        return $elems;
    }

    /**
     * Gets the first result of an xpath expression or false if there
     * isn't any
     */
    public function xpathFirst($query){
        $elms = $this->xpath($query);
        if ( $elms === false ){
            return false;
        }
        return $elms[0];
    }

}

class Search_Parser_Response{
    private $_rawResponse;

    public function __construct($rawResponse){
        $this->_rawResponse = $rawResponse;
    }

    /**
     * Included for compatibility. Use html() and xpath functions
     */
    public function simpleHtmlDom(){
        return new Search_Parser_SimpleHtmlDom($this->_rawResponse->getBody());
    }


    public function html(){
        return new Dom($this->_rawResponse->getBody());
    }

    public function text(){
        return $this->_rawResponse->getBody();
    } 

}
