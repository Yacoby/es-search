<?php
/**
 * Class to allow nicer rending of optionaly plural words
 */
class Zend_View_Helper_Plural{
    /**
     * Optionally adds a 's' to the end of a word
     *
     * @param string $word
     * @param bool $condition
     * @return string
     */
    public function plural($word, $condition){
        return (string)$word . ( $condition ? 's' : '' );
    }
}
