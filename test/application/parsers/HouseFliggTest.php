<?php

class HouseFliggTest extends PHPUnit_Framework_TestCase{
    public function testMods(){
        $factory = new Search_Parser_Factory(APPLICATION_PATH . '/parsers/defaults.ini',
                                             APPLICATION_PATH . '/parsers/parsers.ini');

        $parser = $factory->getByName('HouseFligg');

        $result = $parser->scrape();

        $mods = array(
            array(
                'Name' => 'Blasphemous Revenants',
                'Game' => 'MW',
                'Version' => '2.3',
                'Author' => 'Fliggerty and Friends',
            ),
        );

        $this->modsContains($parser->mods(), $mods);

    }



    private function modsContains($mods, $needles){
        foreach ( $needles as $needle ){
            foreach ( $mods as $mod ){
                foreach ( $neelde as $key => $value ){
                    if ( $mod[$key] != $value ){
                        continue 2;
                    }
                }
                continue 2;
            }
            $this->fail("Couldn't find mod {$needle}");
        }
    }


}
