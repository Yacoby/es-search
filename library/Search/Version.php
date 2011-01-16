<?php
//TODO licence

/*
 * Format. This is assuming a twos complement system, with a 32 bit integer
 *
 * It should work fine with a 64 bit integer
 *
    sign bit. 
    reserved (x3)

    exta bits (x1)
    major bit (x6)
 *
    extra bits (x1)
    minor bit (x6)
 *
    extra bits (x1)
    minor bit (x6)
 *
    extra bits (x1)
    minor bit (x6)
 */

/**
 * The class converts a string version into an integer for comparison in the
 * database rather than server side. This allows us to pull the highest version
 * from the database
 *
 * This class assumes all ints are at least 32 bits and this is working with a twos
 * complement system.
 *
 * This only supports a certain subset of version strings. Version strings can
 * have one predefined string within them. So 0.23.1 beta is acceptable.
 * The results are trunicated after 4 version sections. I have not encountered enough
 * with more than that to be worth supporting.
 *
 * Each version section must be in the range of 0 to 63. More is not supported
 * and will thrown an exception. Again I havn't yet seen any mods with a version
 * greater than 63
 */
class Search_Version {

    /**
     * This is a list of values for str_replace that is run through the string
     *
     * @var array
     */
    private static $_strings = array(
            array('beta', 'b'),
            array('alpha', 'a'),
    );

    public static function fromString($string){
        return self::parseString((string)$string);
    }

    private static function parseString($string) {
        if ( trim($string) == '' ){
            return 0;
        }
        //remove spaces
        $string = trim(str_replace(' ', '', $string));

        //replace_, - and + with a .
        $string = str_replace(array('_', '-', '+'), '.', $string);

        //convert strings to the representation we know what to do with
        foreach ( self::$_strings as $s ){
            $string = str_replace($s[0], $s[1], $string);
        }


        $string = preg_replace("/([a-zA-Z])$/", ".$1 1", $string);
        $string = preg_replace('/([a-zA-Z ]+)([0-9])/', '.$1$2', $string);

        //remove multiple dots and remove them from the ends
        $string = preg_replace('/[\.]+/', '.', $string);
        $string = trim($string, '.');

          //remove spaces
        $string = trim(str_replace(' ', '', $string));

        //holds an array of versions
        $numberBits = explode('.', $string);

        //the output
        $int = 0;

        //copy as many of the versions (4 of them) as we can into the integer
        for ( $i = 3; $i >= 0; $i-- ){

            $val = array_shift($numberBits);

            if ( $val === null ){
                $int = self::setExtraBit($int, $i, 2);
                continue;
            }

            $number = (int)preg_replace('/[a-zA-Z]/', '', $val);
            $letter = preg_replace('/[0-9]/', '', $val);

            $int = self::setVersionBits($int, $i, $number);

            $known = array('a', 'b');
            $index = array_search($letter, $known);
            if ( $index === false ){
                $index = 2; //default
            }
            $int = self::setExtraBit($int, $i, $index);

        }
        return $int;
    }

    private static function setExtraBit($integer, $sectionToSet, $value){
        $value = $value << 7;
        $value = $value << $sectionToSet*7;
        return $integer | $value;
    }
    private static function setVersionBits($integer, $sectionToSet, $value){
            $value = $value << $sectionToSet*8;
            return $integer | $value;
    }
}