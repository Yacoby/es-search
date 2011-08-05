<?php

class HouseFliggTest{
    public function testMods(){
        $factory = new Search_Parser_Factory(APPLICATION_PATH . '/parsers/defaults.ini',
                                             APPLICATION_PATH . '/parsers/parsers.ini');

    }

    private function modsContain($mods, $needle){
        foreach ( $mods as $mod ){
            $found = true;
            foreach ( $needle as $key => $value ){
                if ( $mod[$key] != $value ){
                    $flag = false;
                    break;
                }
            }
            if ( $found ){
                return true;
            }
        }
        return false;
    }
}
