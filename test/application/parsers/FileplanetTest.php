<?php
class FileplanetTest extends PageTest {

    public function __construct() {
        parent::__construct('www.fileplanet.com');
    }

    public function testLinks() {
        $links = array(
            'http://www.fileplanet.com/40796/0/0/0/1/section/Traditional',
            'http://www.fileplanet.com/104681/0/0/0/1/section/Quest_mods',
        );
        $this->assertTrue(
            $this->helpPageHasAnyLinkOf(
                new Search_Url('http://www.fileplanet.com/104054/0/section/Mods'),
                $links
            )
        );


    }

    public function testLinkReplace(){
        $this->assertEquals(
            'http://www.fileplanet.com/104681/0/0/0/1/section/Quest_mods',
            (string)$this->getEmptyPage()
                         ->preAddLink(new Search_Url('http://www.fileplanet.com/104681/0/section/Quest-mods'))
        );
        $this->assertEquals(
            'http://www.fileplanet.com/104299/0/0/0/1/section/Dungeons',
            (string)$this->getEmptyPage()
                         ->preAddLink(new Search_Url('http://www.fileplanet.com/104299/0/section/Dungeons'))
        );
    }

    public function testNotLinks1() {
        $badLins = array(
            'http://www.fileplanet.com/40796/0/0/0/1/section/Traditional',
            "http://www.fileplanet.com/189421/180000/fileinfo/Elder-Scrolls-IV:-Oblivion---Qarl's-Texture-Pack-III-Full-v1.3-OMOD",
        );
        $this->assertFalse(
            $this->helpPageHasAnyLinkOf(
                new Search_Url('http://www.fileplanet.com/40796/0/section/Traditional'),
                $badLins
            )
        );
    }

    public function testNotLinks2() {
        $badLins = array(
            'http://www.fileplanet.com/40796/0/0/0/1/section/Traditional',
            "http://www.fileplanet.com/189421/180000/fileinfo/Elder-Scrolls-IV:-Oblivion---Qarl's-Texture-Pack-III-Full-v1.3-OMOD",
        );
        $this->assertFalse(
            $this->helpPageHasAnyLinkOf(
                new Search_Url('http://www.fileplanet.com/41184/0/section/Mods'),
                $badLins
            )
        );
    }

    public function testMods() {
        $details = array(
            array(
                'Url'     => 'http://www.fileplanet.com/159649/150000/fileinfo/Elder-Scrolls-III:-Morrowind---Kummu-Planation',
                'Name'    => 'Kummu Planation',
                'Author'  => 'greek302',
                'Game'    => 'MW',
                'Category'=> 'Houses',
            ),
            array(
                'Url'     => 'http://www.fileplanet.com/179143/170000/fileinfo/Elder-Scrolls-IV:-Oblivion---Arctic-Gear-Improved',
                'Name'    => 'Arctic Gear Improved',
                'Author'  => 'LHammonds',
                'Game'    => 'OB',
                'Category'=> 'Armor',
            ),
            array(
                'Url'     => 'http://www.fileplanet.com/203577/200000/fileinfo/Elder-Scrolls-IV:-Oblivion--Hanchel-Training-Hovel-v1.2',
                'Name'    => 'Hanchel Training Hovel v1.2',
                'Author'  => 'ModderElGrande',
                'Game'    => 'OB',
                'Category'=> 'Buildings',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
            "http://www.fileplanet.com/216154/210000/fileinfo/Elder-Scrolls-IV:-Oblivion---Nehrim-At-Fate's-Edge-Mod-(English)-v1.0.7.5-Mod",
        );
        $invalid = array();

        $this->helpTestModUrls($valid, $invalid);
    }


}