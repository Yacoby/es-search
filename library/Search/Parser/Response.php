<?php

/**
 * This is a wrapper around DOM document as using it with xpath is a bit of 
 * a pain and 90% of the time all I will be doing is throwing xpath at a dom
 * document
 *
 * This class shouldn't be initated by anything outside this file
 */
class DomElem{
    /*
     * Given that this is mainly a wrapper around xpath (yes, it is) we need
     * to keep the doc element to use with xpath and the element as a context
     * for the xpath
     */
    private $_elem, $_doc;

    public function fromDom($doc, $elem = null){
        $this->_elem = $elem;
        $this->_doc = $doc;
    }

    public function fromHtml($html){
        //this stops warnings being spewed everwhere on malformed html
        libxml_use_internal_errors(true);

        $this->_doc = new DOMDocument();
        $this->_doc->loadHTML($html);

    }

    /**
     * This attempts to allow the use of the Dom* attributes. Not sure how
     * well it works if at all
     */
    public function __get($name){
        $result = $this->_elem->{$name};
        if ( $result instanceof DOMNode ){
            $d = new DomElem();
            $d->fromDom($this->_doc, $result);
            return $d;
        }
        return $result;
    }

    public function __toString(){
        $c = get_class($this->_elem);
        switch ($c) {
            case 'DOMAttr' : return (string)$this->_elem->value; break;
            case 'DOMText' : return (string)$this->_elem->data; break;
        }
        return (string)$this->_elem->textContent;
    }

    /**
     * Returns the text representaion of the dom object, but with new lines
     * removed and all spaces converted into a single space
     */
    public function normalisedString(){
        $str = (string)$this;
        $str = str_replace("\n", ' ', $str);
        return preg_replace('/\s+/', ' ', $str);
    }


    public function xpath($query){
        $xp = new DOMXpath($this->_doc);
        $elems = $xp->query($query, $this->_elem);
        
        if ( $elems === false ){
            return array();
        }
        $domList = array();
        foreach ( $elems as $elem){
            $d = new DomElem();
            $d->fromDom($this->_doc, $elem);
            $domList[] = $d;
        }
        return $domList;
    }

    /**
     * Gets the first result of an xpath expression or false if there
     * isn't any
     */
    public function xpathOne($query){
        $elms = $this->xpath($query);
        if ( !count($elms) ){
            return false;
        }
        return $elms[0];
    }

}

class Search_Parser_Response{
    private $_rawResponse;
    private $_reqUrl;

    public function __construct($reqUrl = null, $rawResponse = null){
        $this->_reqUrl = $reqUrl;
        if ( $rawResponse ){
            $this->_rawResponse = $rawResponse;
        }else{
            $this->_rawResponse = new Zend_Http_Response(200, array());
        }
    }

    /**
     * This returns the url that the page is on. As redirects may happen
     * it returns the actual url that the client ended up on
     */
    public function url(){
        $location = $this->_rawResponse->getHeader('Location');
        if ( $location ){
            return $location;
        }
        return $this->_reqUrl;
    }

    public function httpStatus(){
        return $this->_rawResponse->getStatus();
    }

    /**
     * Included for compatibility. Use html() and xpath functions
     */
    public function simpleHtmlDom(){
        if ( $this->_rawResponse->getBody() ){
            return new Search_Parser_SimpleHtmlDom($this->_rawResponse->getBody());
        }
        return new Search_Parser_SimpleHtmlDom('<html></html>');
    }

    public function html(){
        $tidy = tidy_repair_string($this->_rawResponse->getBody());
        $de = new DomElem();
        $de->fromHtml($tidy);
        return $de;
    }

    public function text(){
        return $this->_rawResponse->getBody();
    } 

}
