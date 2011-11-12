<?php

class ModdingHistoryTest extends PHPUnit_Framework_TestCase{
    public function testMods(){
        $factory = new Search_Parser_Factory(APPLICATION_PATH . '/parsers/defaults.ini',
                                             APPLICATION_PATH . '/parsers/parsers.ini');

        $parser = $factory->getByName('MwModdingHistory');

        $result = $parser->scrape();

        $mods = array(
            array(
                'Name'   => new Search_Unicode('Black Heart Armor'),
                'Game'   => 'MW',
                'Url'    => new Search_Url('http://mw.modhistory.com/download-4-1'),
                'Author' => new Search_Unicode('TheSiriusSnape'),
            ),
            array(
                'Name'        => new Search_Unicode('4 Glass Weapons'),
                'Game'        => 'MW',
                'Url'         => new Search_Url('http://mw.modhistory.com/download-98-22'),
                'Description' => new Search_Unicode('4 Glass Weapons'),
                'Author'      => new Search_Unicode('Unknown'),
            ),
        );

        $this->modsContains($result->mods(), $mods);
    }


    private function modsContains($mods, $needles){
        foreach ( $needles as $needle ){
            $found = false;
            foreach ( $mods as $mod ){
                if ( $this->modIsMatch($mod, $needle) ){
                    $found = true;
                    break;
                }
            }
            if ( !$found ){
                $this->fail("Couldn't find mod {$needle['Name']->getAscii()}");
            }
        }
    }


    private function modIsMatch($mod, $needle){
        foreach ( $needle as $key => $value ){
            if ( !isset($mod[$key]) || $mod[$key] != $value ){
                return false;
            }
        }
        return true;
    }


}
