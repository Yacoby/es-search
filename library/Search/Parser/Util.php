<?php

function chrUtf8($num){
    if ($num < 128) return chr($num);
    if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
    if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    return '';
}

class Search_Parser_Util{
    public static function html_entity_decode_numeric($string, $quote_style = ENT_COMPAT, $charset = "utf-8"){
        $string = html_entity_decode($string, $quote_style, $charset);
        $string = preg_replace_callback('~&#x([0-9a-fA-F]+);~i', create_function('$matches', 'return chrUtf8(hexdec($matches[1])); '), $string);
        $string = preg_replace('~&#([0-9]+);~e', 'chrUtf8("\\1")', $string);
        return $string; 
    }
}


