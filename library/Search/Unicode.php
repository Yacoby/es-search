<?php

/**
 * PHP unicode handling (or lack thereof) does my head in
 *
 * Having unicode strings as a class stops me using bad functions
 * on unicode strings
 */
class Search_Unicode{
    private $_str = null;
    public function __construct($str){
        $this->_str = $str;
    }

    public function __toString(){
        throw new Exception("This isn't possible to do");
    }

    public function htmlEntityDecode(){
        $this->_str = html_entity_decode($this->_str, ENT_COMPAT, 'UTF-8');
    }

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
    }

    public function replace($old, $new){
        $this->_str = str_replace($old, $new, $this->_str);
    }

    /**
     * TODO prob not safe
     */
    public function pregReplace($regex, $new){
        $div = $regex[strlen($regex)-1];
        if ( $div != 'u' ){
            throw new Exception('Regex must have unicode support');
        }
        $this->_str = preg_replace($regex, $new, $this->_str);
    }


    public function getBytes(){
        return $this->_str;
    }

    public function getAscii(){
        return mb_convert_encoding($this->_str, 'ASCII', 'UTF-8');
    }

}
