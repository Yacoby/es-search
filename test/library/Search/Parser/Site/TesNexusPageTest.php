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

require_once "PageHelper.php";


class tesnexus_com_pageTest extends PageTest {

    function __construct() {
        parent::__construct(
                'tesnexus_com',
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=22938')
        );
    }

    function testInstance() {
        $this->helpTestInstance(new Search_Url('http://www.tesnexus.com/downloads/file.php?id=22938'));
    }

    function testMod1() {
        $mod = array(
                'Name'      => 'Pursuit Enhanced',
                'Category'  =>  'Gameplay Effects and Changes',
                'Game'      => 'MW',
                'Author'    => 'Yacoby',
                'Version'   => '1.2.4'
        );
        $this->helpTestModPage(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=22938'),
                1,
                $mod
        );

    }

    /**
     * Indirectly tests strip non ascii
     */
    public function testMod2() {
        $mod = array(
                'Name'      => 'Vivec NPC',
                'Category'  => 'NPCs',
                'Game'      => 'MW',
                'Author'    => 'Arakhor',
        );
        $this->helpTestModPage(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=1647'),
                1,
                $mod
        );
    }

    public function testMod3() {
        $mod = array(
                'Name'      => 'Clean wooden longbow(s) v 2.01',
                'Game'      => 'MW',
                'Author'    => 'Unknown',
        );
        $this->helpTestModPage(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=982'),
                1,
                $mod
        );
    }

    //game failed on this
    public function testMod4() {
        $mod = array(
                'Name'      => 'Early Music Addon',
                'Game'      => 'OB',
                'Author'    => 'lord_equinox',
        );
        $this->helpTestModPage(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=24160'),
                1,
                $mod
        );
    }

    //game was logged as failing on this
    public function testMod5() {
        $mod = array(
                'Name'      => 'UFF Archers Desire',
                'Game'      => 'OB',
                'Author'    => 'Petrovich',
        );
        $this->helpTestModPage(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=13512'),
                1,
                $mod
        );
	}


    public function testMod6() {
        $mod = array(
                'Name'      => 'The Sword of Cyrodiil',
                'Game'      => 'OB',
                'Author'    => 'Jip2',
        );
        $this->helpTestModPage(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=8623'),
                1,
                $mod
        );
    }


    function testURLs() {
        $valid = array(
                'http://www.tesnexus.com/downloads/file.php?id=23065',
                'http://www.tesnexus.com/downloads/file.php?id=23'
        );

        $invalid = array(
                'http://www.tesnexus.com/downloads/file.php?id=d',
                'http://www.tesnexus.com/downloads/file.php?id=',
                'http://www.tesnexus.com/downloads'
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    function testLinks() {
        $links = array(
                'http://www.tesnexus.com/downloads/cat.php?id=15'
        );
        $this->helpRequiredLinks(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=23065'),
                $links
        );
    }

}

?>
