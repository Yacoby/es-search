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
 * l-b */ ?>


<?php
require_once "PageHelper.php";


//ERROR:
//

class planetelderscrolls_comTest extends PageTest {

    public function testLinkStrip() {
        $url = new URL('http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481');
        $page = Search_Parser_Factory::getInstance()->getSiteByURL($url)->getPage($url);

        $url1 = new URL('http://planetelderscrolls.gamespy.com/View.php?category_show_all=1&persist_search=57384ddaa04c4881ff66ab8312ea6d27&view=Mods.List&Data_page=2');
        $url2 = new URL('http://planetelderscrolls.gamespy.com/View.php?category_show_all=1&view=Mods.List&Data_page=2');
        $this->assertEquals(
            $url2->toString(),
            $page->stripFromLinks($url1)->toString()
        );
    }

    function __construct() {
        parent::__construct(
            'planetelderscrolls_com',
            new URL('http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481')
        );
    }

    function testInstance() {
        $this->helpTestInstance(new URL('http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481'));
    }

    function testMod1() {
        $mod = array(
            'Name'      => 'Pursuit Enhanced',
            'Author'    => 'Yacoby',
            'Version'   => '1.2.4',
            'Category'  => 'Tweaks',
            'Game'      => 'MW'
        );
        $this->helpTestModPage(
            new URL('http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481'),
            1,
            $mod
        );

    }
    function testMod2() {
        $mod = array(
            'Name'      => 'Wrye Python',
            'Author'    => 'Wrye',
            'Game'      => 'OB'
        );
        $this->helpTestModPage(
            new URL('http://planetelderscrolls.gamespy.com/View.php?view=OblivionUtilities.Detail&id=44'),
            1,
            $mod
        );
    }

    function testMod3() {
        $mod = array(
            'Name'      => 'Mod Delayers and Mod Tweaks',
            'Author'    => 'Aellis',
            'Game'      => 'OB',
            'Version'   => '1.03',
            'Category'   => 'Tweaks',
        );
        $this->helpTestModPage(
            new URL('http://planetelderscrolls.gamespy.com/View.php?view=OblivionMods.Detail&id=5770'),
            1,
            $mod
        );
    }

    function testMod4() {
        $mod = array(
            'Name'      => 'At Home Alchemy',
            'Author'    => 'Syclonix',
            'Game'      => 'OB',
            'Version'   => '1.1',
            'Category'   => 'Alchemical',
        );
        $this->helpTestModPage(
            new URL('http://planetelderscrolls.gamespy.com/View.php?view=OblivionMods.Detail&id=1446'),
            1,
            $mod
        );
    }

    function testMod5() {
        $mod = array(
            'Name'      => 'Hand to hand spell casting animations',
            'Author'    => 'Resurrection',
            'Game'      => 'OB',
            'Category'   => 'Miscellaneous',
        );
        $this->helpTestModPage(
            new URL('http://planetelderscrolls.gamespy.com/View.php?view=OblivionMods.Detail&id=5792'),
            1,
            $mod
        );
    }

    function testURLs() {
        $valid = array(
            'http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481',
        );
        $invalid = array(
            'http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=mymod',
        );
        $this->helpTestModUrls($valid, $invalid);
    }




}

?>
