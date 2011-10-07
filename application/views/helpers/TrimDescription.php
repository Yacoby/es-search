<?php
/**
 * Helper class desinged to try and stop words getting cut off mid word
 */
class Zend_View_Helper_TrimDescription{

    /**
     * Ensures a string is under 255 characters. If it exceeds 255 characters
     * it takes the substring from 0 to the last space in the range 0 < x < 255
     *
     * @param string $str
     * @return string
     */
    public function trimDescription($str){
        $str = (string)$str;

        if ( strlen($str) < 255 ){
            return $str;
        }

        $index = strripos($str, ' ', 255-strlen($str));
        if ( $index === false ){
            return trim(substr($str,0,255)) . '...';
        }
        return trim(substr($str,0,$index)) . '...';
    }
}
