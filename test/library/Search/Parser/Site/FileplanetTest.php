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

    function testModUrls(){
        $valid = array(
            "http://www.fileplanet.com/216154/210000/fileinfo/Elder-Scrolls-IV:-Oblivion---Nehrim-At-Fate's-Edge-Mod-(English)-v1.0.7.5-Mod",
        );
        $invalid = array();

        $this->helpTestModUrls($valid, $invalid);
    }

    function testLinks(){
        $links = array(
            'http://www.fileplanet.com/40796/0/0/0/1/section/Traditional',
            'http://www.fileplanet.com/104681/0/0/0/1/section/Quest-mods',
        );
        $this->assertTrue(
                $this->helpHasAnyLinkOf(
                        new Search_Url('http://www.fileplanet.com/104054/0/section/Mods'),
                        $links
                )
        );
    }
    function testNotLinks1(){
        $badLins = array(
            'http://www.fileplanet.com/40796/0/0/0/1/section/Traditional',
            "http://www.fileplanet.com/189421/180000/fileinfo/Elder-Scrolls-IV:-Oblivion---Qarl's-Texture-Pack-III-Full-v1.3-OMOD",
        );
        $this->assertFalse(
                $this->helpHasAnyLinkOf(
                        new Search_Url('http://www.fileplanet.com/40796/0/section/Traditional'),
                        $badLins
                )
        );
    }

    function testNotLinks2(){
        $badLins = array(
            'http://www.fileplanet.com/40796/0/0/0/1/section/Traditional',
            "http://www.fileplanet.com/189421/180000/fileinfo/Elder-Scrolls-IV:-Oblivion---Qarl's-Texture-Pack-III-Full-v1.3-OMOD",
        );
        $this->assertFalse(
                $this->helpHasAnyLinkOf(
                        new Search_Url('http://www.fileplanet.com/41184/0/section/Mods'),
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
    function testMod3(){
        $mod = array(
            'Name'    => 'Hanchel Training Hovel v1.2',
            'Author'  => 'ModderElGrande',
            'Game'    => 'OB',
            'Category'=> 'Buildings',
        );
        $this->helpTestModPage(
            new Search_Url('http://www.fileplanet.com/203577/200000/fileinfo/Elder-Scrolls-IV:-Oblivion--Hanchel-Training-Hovel-v1.2'),
            1,
            $mod
        );
    }
}