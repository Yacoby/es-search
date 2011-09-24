<?php

/**
 * PHP unicode handling (or lack thereof) does my head in
 *
 * Having unicode strings as a class stops me using bad functions
 * on unicode strings
 *
 * This is stored internally as utf8, as this is what the db uses
 */
class Search_Unicode{
    private $_str = null;
    public function __construct($str, $encoding='UTF-8'){
        if ( $encoding == 'UTF-8' ){
            $this->_str = $str;
        }else{
            $this->_str = iconv($encoding, 'UTF-8//TRANSLIT', $str);
        }
    }

    /**
     * We can't do this, as it isn't explicit what we want from the string
     */
    public function __toString(){
        throw new Exception("This isn't possible to do");
    }

    public function htmlEntityDecode(){
        $this->_str = html_entity_decode($this->_str, ENT_COMPAT, 'UTF-8');
        return $this;
    }

    /**
     * @param $charlist has to be ascii
     */
    public function trim($charlist = null){
        if ( $charlist != null ){
            for ( $i = 0; $i < strlen($charlist); $i++ ){
                $char = $charlist[$i];
                if ( ord($char) >= 128 ){
                    throw new Exception('Cannot use trim with non ascii');
                }
            }
            $this->_str = trim($this->_str, $charlist);
        }
        $this->_str = trim($this->_str);
        return $this;
    }

    /**
     * This sometimes is a bit derp, to debug use the pregReplace function
     * which seems to give more warnings as the expression is compiled as
     * unicode with can test for errors
     */
    public function replace($old, $new){
        //return $this->pregReplace("~{$old}~u", $new);
        $this->_str = str_replace($old, $new, $this->_str);
        return $this;
    }

    /**
     * This strips the tags from a UTF-8 string. Doesn't work on other mb
     * encodings
     */
    public function stripTags(){
        if ( mb_detect_encoding($this->_str, 'UTF-8') == false ){
            throw new Exception('Strip tags only works on utf-8');
        }
        $this->_str = strip_tags($this->_str);
        return $this;
    }

    /**
     * This isn't great. There are some issues with utf8 and preg_replace
     * which this function doesn't solve
     */
    public function pregReplace($regex, $new){
        $div = $regex[strlen($regex)-1];
        if ( $div != 'u' ){
            throw new Exception('Regex must have unicode support');
        }
        $this->_str = preg_replace($regex, $new, $this->_str);
        return $this;
    }

    public function getBytes(){
        return $this->_str;
    }

    public function getAscii(){
        return mb_convert_encoding($this->_str, 'ASCII', 'UTF-8');
    }

}
