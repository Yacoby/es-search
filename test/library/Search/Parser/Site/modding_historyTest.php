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


/*
require_once ("Parser/Parser.php");

class modding_history_pageTest1 extends PHPUnit_Framework_TestCase {

    private $page;
    protected function setUp() {
        $url = new URL('http://modhistory.fliggerty.com/rwdownload/index.php?dlid=3848');
        $this->page = Search_Parser_Factory::getInstance()->getSiteByURL($url)->getPage($url);
    }

    public function testTypeCorrect() {
        $this->assertTrue($this->page instanceof modding_history_page);
    }

    public function testGrabMods() {
        $mods = $this->page->mods();
        $mod = $mods[0];

        $this->assertEquals('Potion Upgrades', $mod['Name']);
        $this->assertEquals('Schwaa', $mod['Author']);
        $this->assertEquals('Alchemical', $mod['Category']);

        $this->assertEquals('Changes the potions (meshes and textures) to look much more stylized. The potions will even have bubbles floating inside the bottle.', $mod['Description']);
    }

    public function testURLs() {
        $p = $this->page;

        $this->assertTrue(
            $p->isValidModPage(new URL('http://modhistory.fliggerty.com/rwdownload/index.php?dlid=2944'))
        );
        $this->assertFalse(
            $p->isValidModPage(new URL('http://modhistory.fliggerty.com/rwdownload/index.php?dlid=jd'))
        );
        $this->assertTrue(
            $p->isValidPage(new URL('http://modhistory.fliggerty.com/rwdownload/index.php?cid=8'))
        );
        $this->assertTrue(
            $p->isValidPage(new URL('http://modhistory.fliggerty.com/rwdownload/index.php?cid=25&sortvalue=date&order=ASC&limit=30'))
        );

    }


}
*/
?>
