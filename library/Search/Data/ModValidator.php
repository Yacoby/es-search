<?php /* l-b
 * This file is part of ES Search.
 * 
 * Copyright (c) 2009 Jacob Essex
 * 
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ES Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with ES Search. If not, see <http://www.gnu.org/licenses/>.
 * l-b */

require dirname(__FILE__).'/Req/UTF8Hack.php';

/**
 * Thrown because the mod passed is invalid
 */
class ModValidatorException extends Exception {

}

/**
 * A class that validates mods
 *
 * @todo Could do it all with statics, except my php ver doesn't support __callStatic
 */
class Search_Data_ModValidator {

    private static $_required = array(
            'Game',
            'Name',
            'Author',
            'URL',
    );

    /**
     * List of maximum lengths
     * @var array
     */
    private static $_maxlen = array(
            'Name' => 255,
            'Author' => 255,
            'URL' => 511,
            'Version' => 31,
            'Category' => 63
    );

    private static $_minlen = array(
            'Name' => 1,
            'Author' => 1,
    );
    /**
     *
     * Condenses the text into a hopefully smaller form
     * @param string $text
     * @return string
     */
    private static function stripText($text) {
        if ( mb_check_encoding($text) == "UTF-8" ) {
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            //$text = iconv("UTF-8", "ISO-8859-1//IGNORE", $text);
        }else {
            $text = html_entity_decode($text, ENT_QUOTES);
        }

        //strips non breaking spaces and replaces them with spaces
        $text = str_replace("\xa0", "\x20", $text);

        //we don't need (or want) links, etc. Also deals with html comments
        $text = strip_tags($text);

        //ofc
        $text = trim($text);

        return $text;
    }

    /**
     * Ensures that all required keys are present
     *
     * @param array $modDetails
     * @throws ModValidatorException
     */
    public function checkHasRequiredKeys(array $modDetails) {

        foreach ( self::$_required as $r ) {
            if ( !array_key_exists($r, $modDetails)) {
                throw new ModValidatorException("Lacking required data ($r)");
            }
        }
    }

    /*
     * Overloads for the handinling in __call for the custom stuff
    */

    public function validateDescription($text) {
        return self::stripText($text);
    }

    public function validateURL(URL $url) {
        $name = 'URL';
        if ( strlen($url->toString()) > self::$_maxlen[$name] )
            throw new ModValidatorException(
            $name.' is not valid as it exceeds '.ModValidator::$_maxlen[$name]
            );
        return $url;
    }

    public function validateGame($game) {
        $v = array('OB', 'MW', 'UN');
        if ( !in_array($game, $v) ) {
            throw new ModValidatorException(
            $name.' is not valid as it is not a game'
            );
        }
        return $game;
    }

    /**
     * Valids a string, based on the $_maxlen array, returing the new version
     *
     * @throws ModValidatorException, Exception
     */
    public static function __call($name, $args) {
        if ( count($args) != 1 ) {
            throw new Exception(
            "Invalid number of args. Expecting a single string"
            );
        }
        $arg = $args[0];

        if ( strpos($name, "validate") != 0 ) {
            throw new Exception("Function doesn't exist");
        }

        $name = substr($name, strlen("validate"));

        if ( !array_key_exists($name, self::$_maxlen) ) {
            throw new Exception("Function doesn't exist");
        }

        $arg = self::stripText($arg);
        $len = strlen($arg);

        if ( $len > self::$_maxlen[$name] ) {
            throw new ModValidatorException(
            $name.' is not valid as its length exceeds '.self::$_maxlen[$name]
            );
        }

        if ( array_key_exists($name, self::$_minlen) ) {
            if ( $len < self::$_minlen[$name]  ) {
                throw new ModValidatorException(
                $name.' is not valid as its length is below '.self::$_minlen[$name]
                );
            }
        }

        return $arg;
    }




}

