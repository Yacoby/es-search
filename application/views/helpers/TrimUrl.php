<?php
/**
 * Helper class to ensure a url is an acceptable length to be printed. If it
 * isn't, it removed the middle of it.
 *
 */
class Zend_View_Helper_TrimUrl{
    /**
     * Ensure the url is less than about 64 characters
     *
     * @param string $string
     * @return string
     */
    public function trimUrl($string){
        $string = (string)$string;
        if ( strlen($string) < 64 ){
            return $string;
        }
        return substr($string, 0, 32) .'... &nbsp ...'. substr($string,-24);
    }
}
