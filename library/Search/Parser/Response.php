<?php

/**
 * This is a wrapper around DOM document as using it with xpath is a bit of 
 * a pain and 90% of the time all I will be doing is throwing xpath at a dom
 * document
 *
 * This class shouldn't be initated by anything outside this file
 */
class DomElem{
    private $_dom;

    public function fromDom($dom){
        $this->_dom = $dom;
    }

    public function fromHtml($html){
        //this stops warnings being spewed everwhere on malformed html
        libxml_use_internal_errors(true);

        $this->_dom = new DOMDocument();
        $this->_dom->loadHTML($html);

    }

    /**
     * Bit derp. Only DOMAttr has the value attribute
     */
    public function __toString(){
        $c = get_class($this->_dom);
        switch ($c) {
            case 'DOMAttr' : return (string)$this->_dom->value; break;
            case 'DOMText' : return (string)$this->_dom->data; break;
        }
        return (string)$this->_dom->textContent;
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
        $xp = new DOMXpath($this->_dom);
        $elems = $xp->query($query);
        
        if ( $elems === false ){
            return array();
        }
        $domList = array();
        foreach ( $elems as $elem){
            $d = new DomElem();
            $d->fromDom($elem);
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
