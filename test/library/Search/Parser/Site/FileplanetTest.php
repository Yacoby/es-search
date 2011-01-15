<?php

require_once "PageHelper.php";

class FileplanetTest extends PageTest {
    function __construct(){
        parent::__construct(
            'Fileplanet',
            new Search_Url('http://www.fileplanet.com/159649/150000/fileinfo/Elder-Scrolls-III:-Morrowind---Kummu-Planation')
        );
    }
    function testInstance(){
        $this->helpTestInstance(
                new Search_Url('http://www.fileplanet.com/159649/150000/fileinfo/Elder-Scrolls-III:-Morrowind---Kummu-Planation')
        );
    }

    function tesetLinks(){
        $badLins = array(
            'http://www.fileplanet.com/40796/0/section/Traditional',
            "http://www.fileplanet.com/189421/180000/fileinfo/Elder-Scrolls-IV:-Oblivion---Qarl's-Texture-Pack-III-Full-v1.3-OMOD",
        );
        $this->assertFalse(
                $this->helpHasAnyLinkOf(
                        new Search_Url('http://www.fileplanet.com/40796/0/section/Traditional'),
                        $badLins
                )
        );

    }

    function testMod1(){
        $mod = array(
            'Name'    => 'Kummu Planation',
            'Author'  => 'greek302',
            'Game'    => 'MW',
            'Category'=> 'Houses',
        );
        $this->helpTestModPage(
            new Search_Url('http://www.fileplanet.com/159649/150000/fileinfo/Elder-Scrolls-III:-Morrowind---Kummu-Planation'),
            1,
            $mod
        );
    }
    function testMod2(){
        $mod = array(
            'Name'    => 'Arctic Gear Improved',
            'Author'  => 'LHammonds',
            'Game'    => 'OB',
            'Category'=> 'Armor',
        );
        $this->helpTestModPage(
            new Search_Url('http://www.fileplanet.com/179143/170000/fileinfo/Elder-Scrolls-IV:-Oblivion---Arctic-Gear-Improved'),
            1,
            $mod
        );
    }
}