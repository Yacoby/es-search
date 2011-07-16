<?php

final class Search_Url {
    /**
     * @var string
     */
    private $_url;

    /**
     * @var boolean
     */
    private $_valid = true;

    /**
     *
     * @param string $url The url. The url can be relative if the current
     *                    url ($currentURL) is set
     * @param Search_Url $currentURL
     */
    public function __construct($url, Search_Url $currentURL = null) {
        if ( $currentURL === null ) {
            #print "COnstruct String\n";
            $this->constructString($url);
        }else {
            #print "COnstruct Rel\n";
            $this->constructRel($url, $currentURL);
        }
    }

    private function constructString($url) {
        if ( $this->isValidURL($url) ) {
            $this->_url   = $url;
        }else {
            $this->_valid = false;
        }
    }

    /**
     * Constructs a URL from the current url. Even if the url is ../index.php
     */
    private function constructRel($url, Search_Url  $currentURL) {
        if ( !$currentURL->isValid() ) {
            #print "Not Valid1\n";
            $this->_valid = false;
        }else if ( $url == 'http://' ) {
            #print "Not Valid2\n";
            $this->_valid = false;
        }else {
            //try {
            #print "From {$url} and given {$currentURL}\n";
                $url = $this->parsePartialUrl($url, $currentURL->toString());
                #print $url . "\n";
                $this->constructString($url);
            //}catch(Exception $e ) {
            //    $this->_valid = false;
            //}

        }

    }

    private function isRelative($url){
        return parse_url($url, PHP_URL_SCHEME) == NULL;
    }

    /**
     *
     * @param string $relative
     * @param string $absolute
     * @return string
     */
    private function parsePartialUrl($relative, $absolute) {
        if ( !$this->isRelative($relative) ){
            return $relative;
        }

        if ( strlen($relative) == 0 ){
            return $absolute;
        }

        $parsedAbs = parse_url($absolute);
        $parsedRel = parse_url($relative);

        if ( !isset($parsedAbs['path']) ){
            $parsedAbs['path'] = ''; 
        }

        //absolute url, but that may contain ../ or // or /./
        if ( isset($parsedRel['path']) && !empty($parsedRel['path']) ){
            //strip last filename in the path
            $parsedAbs['path'] = preg_replace('#/[^/]*$#',
                                              '',
                                              $parsedAbs['path']);
            if ( $parsedRel['path'][0] == '/'){
                $parsedAbs['path'] = '';
            }
            $parsedAbs['path'] .= '/' . $parsedRel['path'];
            $parsedAbs['query'] = $parsedAbs['fragment'] = '';
        }

        //copy across query path etc
        foreach ( array('query', 'fragment') as $k ){
            if ( isset($parsedRel[$k]) ){
                $parsedAbs[$k] = $parsedRel[$k];
            }
        }

        return $this->cleanUrl($parsedAbs);
    }

    private function cleanUrl($url){
        if ( is_array($url) ){
            $urlParts = $url;
        }else{
            $urlParts = parse_url((string)$url);
        }

        //replace '//' or '/./' or '/foo/../' with '/'
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        $n = 1; //number of replacements
        do{
            $urlParts['path'] = preg_replace($re, //look for any of these
                                             '/', //replace with this
                                             $urlParts['path'],
                                             -1, //no limit to number of replaces
                                             $n); //load n with num of replaces
        }while($n); //while a replacement has been made

        return $this->constructFromParts($urlParts);
    }

    private function constructFromParts(array $p){
        $url = $p['scheme'] . '://';

        if ( isset($p['user']) ){
            $url .= $p['user'];
            if ( isset($p['pass']) ){
                $url .= $p['pass'];
            }
            $url .= '@';
        }
        
        $url .= $p['host'];
        if ( isset($p['port']) ){
            $url .= ':' . $p['port'];
        }

        if ( isset($p['path']) ){
            $url .= $p['path'];
        }

        if ( isset($p['query']) && strlen($p['query']) ){
            $url .= '?' . $p['query'];
        }

        if ( isset($p['fragment']) && strlen($p['fragment']) ){
            $url .= '#' . $p['fragment'];
        }

        return $url;
    }

    public function isValid() {
        return $this->_valid;
    }

    public function getHost() {
        assert($this->_valid);
        return parse_url($this->_url, PHP_URL_HOST);
    }

	public function getGet(){
		$hloc = stripos($this->toString(), $this->getHost());
		if ( $hloc === false ){
			throw new Exception('The host was not found in the Url. WTF?');
		}
		$hloc += strlen($this->getHost());
		return substr($this->toString(),$hloc);
	}

    public function toString() {
        return $this->_url;
    }

    public function __toString() {
        return (string)$this->toString();
    }

    protected function isValidUrl($url) {
        return Zend_Uri::check($url);
    }

}