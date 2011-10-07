<?php
/**
 * Converts a string into a string containing only valid charachters, converting
 * a space into - and stripping anything not a-zA-Z0-9
 */
class Zend_View_Helper_SeoString{
    public function seoString($str){
            $str = preg_replace('/[^a-zA-Z0-9\s]/', '', (string)$str);
            $str = preg_replace('/\s+/', ' ', $str);
            return str_replace(' ', '-', $str);
    }
}
