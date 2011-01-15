<?php
//TODO licence

/*
 * Format. This is assuming a twos complement system, with a 32 bit integer
 *
 * It should work fine with a 64 bit integer
 *
    sign bit. 
    reserved (x3)

    major bit (x6)
    minor bit (x6)
    minor bit (x6)
    minor bit (x6)

    reserved  (x4)
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
     * @var array
     */
    /*
    private static $_strings = array(
            array('rc', 'r'),
            array('beta', 'b'),
            array('alpha', 'a'),
            array('dev', 'd'),
    );
     * 
     */

    public static function fromString($string){
        return self::parseString((string)$string);
    }

    private static function parseString($string) {
        //remove spaces
        $string = str_replace(' ', '', $string);

        //replace_, - and + with a .
        $string = str_replace(array('_', '-', '+'), '.', $string);

        //convert strings to the representation we know what to do with
        //foreach ( self::$_strings as $s ){
        //    $string = str_replace($s[0], $s[1], $string);
        //}

        //remove the string and add a dot so that 1beta3 becomes 1.3
        $string = preg_replace('/([0-9])?([a-zA-Z]+)([0-9])?/', '$1.$3', $string);
        $string = trim($string, '.');

        //holds the last string value, converted to its ineteger format
        //$stringBits = 0;
        //holds an array of versions
        $numberBits = array();

        foreach ( explode('.', $string) as $v ) {
           // if ( is_numeric($v) ) {
                $numberBits[] = self::getIntVersionBits((int)$v);
           // }else {
           //     $stringBits = self::getStrVersionBits((string)$v, $stringBits);
           // }
        }

        //the output
        $int = 0;

        //copy as many of the versions (4 of them) as we can into the integer
        for ( $i = 3; $i >= 0; $i-- ){
            $val = array_shift($numberBits);
            if ( $val === null ){
                break;
            }

            $val = $val << 4; //shift is past the first reserved section

            $val = $val << $i*6;
            $int = $int | $val; 
        }

        /*
        //if a string is set, add in the string and make the number negative
        if ( $stringBits !== 0 ){
            //shift it up to the start of the bits
            $stringBits = $stringBits << 27;
            $int = $int | $stringBits;

            //remember that stringBits isn't negative, but if
            //a string is in the version, the result needs to be
            $int = -$int;
        }
         
         */

        return $int;
    }
    /**
     * This takes a string and tries to convert it into the required integeer.
     * It is NOT negative. So the function that uses this needs to swap the
     * integer to negative if the result from running this is != 0
     *
     * @param string $string
     * @param int $current   The current value. Returned if the $string isn't valid
     * @return int
     */
    /*
    private static function getStrVersionBits($string, $current) {
        //ordering is very important
        $known = array('r', 'b', 'a', 'd');
        $index = array_search($string, $known);
        if ( $index === false ){
            return $current;
        }
        return 1 << $index;

     }
     */
    private static function getIntVersionBits($v) {
        if ( $v < 0 || $v > 63 ) {
            throw new Search_Version_Exception('Cannot have versions less than 0 or exceeding 63');
        }
        return (int)$v;
    }
}